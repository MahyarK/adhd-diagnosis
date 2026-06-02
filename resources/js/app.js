const root = document.querySelector('.shell');

if (root) {
    const sections = JSON.parse(root.dataset.sections);
    const ui = JSON.parse(root.dataset.ui);
    const questions = sections.flatMap((section) => section.questions.map((question) => ({ ...question, section })));
    const answers = {};
    let index = 0;
    let autoAdvanceTimer = null;

    const progressLabel = document.getElementById('progressLabel');
    const progressBar = document.getElementById('progressBar');
    const sectionLabel = document.getElementById('sectionLabel');
    const sectionIntro = document.getElementById('sectionIntro');
    const questionText = document.getElementById('questionText');
    const answerGrid = document.getElementById('answerGrid');
    const nextButton = document.getElementById('nextButton');
    const backButton = document.getElementById('backButton');
    const resultsPanel = document.getElementById('resultsPanel');
    const sectionRail = document.getElementById('sectionRail');
    const pointsLabel = document.getElementById('pointsLabel');
    const streakLabel = document.getElementById('streakLabel');
    const motivationLabel = document.getElementById('motivationLabel');
    const findCliniciansButton = document.getElementById('findCliniciansButton');
    const clinicianMapPanel = document.getElementById('clinicianMapPanel');
    const clinicianStatus = document.getElementById('clinicianStatus');
    const clinicianList = document.getElementById('clinicianList');
    const fallbackMapLink = document.getElementById('fallbackMapLink');
    let clinicianMap = null;

    const frequencyOptions = [
        ['0', ui.frequency.never, ui.frequency.never_detail],
        ['1', ui.frequency.rarely, ui.frequency.rarely_detail],
        ['2', ui.frequency.sometimes, ui.frequency.sometimes_detail],
        ['3', ui.frequency.often, ui.frequency.often_detail],
        ['4', ui.frequency.very_often, ui.frequency.very_often_detail],
    ];

    function renderRail() {
        const currentSectionIndex = sections.findIndex((section) => questions[index].section.key === section.key);

        sectionRail.innerHTML = sections.map((section, sectionIndex) => {
            const current = questions[index].section.key === section.key ? 'active' : '';
            const done = sectionIndex < currentSectionIndex ? 'done' : '';

            return `<div class="rail-item ${current} ${done}"><span></span>${section.label}</div>`;
        }).join('');
    }

    function renderQuestion() {
        clearTimeout(autoAdvanceTimer);

        const question = questions[index];
        const options = question.kind === 'choice'
            ? Object.entries(question.options).map(([value, label]) => [value, label, ''])
            : frequencyOptions;
        const answeredCount = Object.keys(answers).length;

        progressLabel.textContent = `${index + 1} / ${questions.length}`;
        progressBar.style.width = `${((index + 1) / questions.length) * 100}%`;
        sectionLabel.textContent = question.section.label;
        pointsLabel.textContent = answeredCount * 10;
        streakLabel.textContent = answeredCount > 0 ? ui.answered.replace(':count', answeredCount) : ui.ready;
        motivationLabel.textContent = ui.encouragement[index % ui.encouragement.length];
        sectionIntro.textContent = question.section.intro;
        questionText.textContent = question.text;
        backButton.disabled = index === 0;
        nextButton.textContent = index === questions.length - 1 ? ui.see_results : ui.skip;

        answerGrid.innerHTML = options.map(([value, label, detail]) => {
            const selected = String(answers[question.id] ?? '') === value ? 'selected' : '';

            return `
                <button type="button" class="answer-option ${selected}" data-value="${value}">
                    <strong>${label}</strong>
                    ${detail ? `<span>${detail}</span>` : ''}
                </button>
            `;
        }).join('');

        answerGrid.querySelectorAll('button').forEach((button) => {
            button.addEventListener('click', () => {
                answers[question.id] = button.dataset.value;
                renderQuestion();
                autoAdvanceTimer = setTimeout(() => advance(), 180);
            });
        });

        renderRail();
    }

    function advance() {
        const question = questions[index];

        if (answers[question.id] === undefined) {
            answerGrid.classList.add('needs-answer');
            motivationLabel.textContent = ui.choose_to_unlock;
            setTimeout(() => answerGrid.classList.remove('needs-answer'), 450);

            return;
        }

        if (index === questions.length - 1) {
            submitAnswers();

            return;
        }

        index += 1;
        renderQuestion();
    }

    async function submitAnswers() {
        nextButton.disabled = true;
        nextButton.textContent = ui.scoring;

        const response = await fetch(root.dataset.scoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ answers }),
        });

        if (! response.ok) {
            nextButton.disabled = false;
            nextButton.textContent = ui.see_results;

            return;
        }

        showResults(await response.json());
    }

    function showResults(result) {
        document.querySelector('.assessment-layout').hidden = true;
        resultsPanel.hidden = false;
        document.getElementById('resultTitle').textContent = result.title;
        document.getElementById('resultSummary').textContent = result.summary;
        document.getElementById('resultDisclaimer').textContent = result.disclaimer;
        document.getElementById('inattentiveCount').textContent = `${result.counts.inattentive} of 9`;
        document.getElementById('hyperactiveCount').textContent = `${result.counts.hyperactive} of 9`;
        document.getElementById('thresholdUsed').textContent = `${result.threshold}+`;
        document.getElementById('scoreSummary').textContent = ui.score_summary
            .replace(':inattentive', result.counts.inattentive)
            .replace(':hyperactive', result.counts.hyperactive)
            .replace(':threshold', result.threshold);

        const labels = {
            childhood: ui.context.childhood,
            settings: ui.context.settings,
            impairment: ui.context.impairment,
            duration: ui.context.duration,
        };

        document.getElementById('contextGrid').innerHTML = Object.entries(result.context).map(([key, value]) => `
            <div class="${value ? 'met' : 'missing'}">
                <span>${labels[key]}</span>
                <strong>${value ? ui.present : ui.needs_clarity}</strong>
            </div>
        `).join('');

        renderProfile(result.profile);

        document.getElementById('nextSteps').innerHTML = result.next_steps
            .map((step) => `<li>${step}</li>`)
            .join('');
    }

    function renderProfile(profile) {
        document.getElementById('profileReport').innerHTML = `
            <article class="explain-card wide">
                <h3>${profile.plain_title}</h3>
                <p>${profile.plain}</p>
                <p class="kind-note">${profile.not_character}</p>
            </article>
            ${renderListCard(profile.daily_title, profile.daily)}
            ${renderListCard(profile.strengths_title, profile.strengths)}
            ${renderListCard(profile.handles_title, profile.handles, 'wide')}
            ${renderListCard(profile.clinician_title, profile.clinician)}
            ${renderListCard(profile.important_title, profile.important, 'important')}
        `;
    }

    function renderListCard(title, items, extraClass = '') {
        return `
            <article class="explain-card ${extraClass}">
                <h3>${title}</h3>
                <ol>
                    ${items.map((item) => `<li>${item}</li>`).join('')}
                </ol>
            </article>
        `;
    }

    nextButton.addEventListener('click', () => {
        advance();
    });

    backButton.addEventListener('click', () => {
        clearTimeout(autoAdvanceTimer);
        index = Math.max(0, index - 1);
        renderQuestion();
    });

    document.getElementById('restartButton').addEventListener('click', () => {
        window.location.reload();
    });

    findCliniciansButton.addEventListener('click', () => {
        showClinicianMap();
    });

    renderQuestion();

    async function showClinicianMap() {
        clinicianMapPanel.hidden = false;
        findCliniciansButton.disabled = true;
        clinicianStatus.textContent = ui.map_requesting_location;
        clinicianMapPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });

        if (! navigator.geolocation) {
            showMapFallback(ui.map_location_unavailable);

            return;
        }

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const { latitude, longitude } = position.coords;

                fallbackMapLink.href = `https://www.google.com/maps/search/ADHD+psychologist+psychiatrist+near+me/@${latitude},${longitude},13z`;
                clinicianStatus.textContent = ui.map_loading;

                try {
                    await loadLeaflet();
                    renderMap(latitude, longitude);
                    const clinicians = await fetchNearbyClinicians(latitude, longitude);
                    renderClinicians(clinicians, latitude, longitude);
                } catch (error) {
                    showMapFallback(ui.map_lookup_failed);
                }
            },
            () => showMapFallback(ui.map_permission_denied),
            { enableHighAccuracy: false, maximumAge: 300000, timeout: 12000 },
        );
    }

    function showMapFallback(message) {
        findCliniciansButton.disabled = false;
        clinicianStatus.textContent = message;
        clinicianList.innerHTML = `
            <a class="clinician-card" href="https://www.google.com/maps/search/ADHD+psychologist+psychiatrist+near+me" target="_blank" rel="noreferrer">
                <strong>${ui.open_maps}</strong>
                <span>${ui.map_fallback_copy}</span>
            </a>
        `;
    }

    async function loadLeaflet() {
        if (window.L) {
            return;
        }

        await new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    function renderMap(latitude, longitude) {
        if (clinicianMap) {
            clinicianMap.remove();
        }

        clinicianMap = L.map('clinicianMap', { scrollWheelZoom: false }).setView([latitude, longitude], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
        }).addTo(clinicianMap);
        L.marker([latitude, longitude]).addTo(clinicianMap).bindPopup(ui.your_location);
    }

    async function fetchNearbyClinicians(latitude, longitude) {
        const radius = 15000;
        const query = `
            [out:json][timeout:25];
            (
              node(around:${radius},${latitude},${longitude})["healthcare"~"psychologist|psychiatrist|psychotherapist|doctor|clinic"];
              way(around:${radius},${latitude},${longitude})["healthcare"~"psychologist|psychiatrist|psychotherapist|doctor|clinic"];
              relation(around:${radius},${latitude},${longitude})["healthcare"~"psychologist|psychiatrist|psychotherapist|doctor|clinic"];
              node(around:${radius},${latitude},${longitude})["amenity"~"doctors|clinic|hospital"]["name"];
              way(around:${radius},${latitude},${longitude})["amenity"~"doctors|clinic|hospital"]["name"];
              relation(around:${radius},${latitude},${longitude})["amenity"~"doctors|clinic|hospital"]["name"];
            );
            out center tags 50;
        `;
        const response = await fetch('https://overpass-api.de/api/interpreter', {
            method: 'POST',
            body: new URLSearchParams({ data: query }),
        });

        if (! response.ok) {
            throw new Error('Overpass lookup failed');
        }

        const data = await response.json();

        return data.elements
            .map((element) => {
                const lat = element.lat ?? element.center?.lat;
                const lon = element.lon ?? element.center?.lon;

                if (! lat || ! lon) {
                    return null;
                }

                return {
                    id: `${element.type}-${element.id}`,
                    name: element.tags?.name ?? ui.unnamed_clinician,
                    kind: element.tags?.healthcare ?? element.tags?.amenity ?? ui.clinician,
                    lat,
                    lon,
                    distance: distanceKm(latitude, longitude, lat, lon),
                };
            })
            .filter(Boolean)
            .sort((a, b) => a.distance - b.distance)
            .slice(0, 40);
    }

    function renderClinicians(clinicians, latitude, longitude) {
        if (clinicians.length === 0) {
            clinicianStatus.textContent = ui.map_no_results;
            showMapFallback(ui.map_no_results);

            return;
        }

        clinicianStatus.textContent = ui.map_results.replace(':count', clinicians.length);
        clinicianList.innerHTML = clinicians.map((clinician) => `
            <a class="clinician-card" href="https://www.google.com/maps/search/?api=1&query=${clinician.lat},${clinician.lon}" target="_blank" rel="noreferrer">
                <strong>${clinician.name}</strong>
                <span>${clinician.kind} · ${clinician.distance.toFixed(1)} km</span>
            </a>
        `).join('');

        clinicians.forEach((clinician) => {
            L.marker([clinician.lat, clinician.lon])
                .addTo(clinicianMap)
                .bindPopup(`<strong>${clinician.name}</strong><br>${clinician.kind}<br>${clinician.distance.toFixed(1)} km`);
        });

        clinicianMap.fitBounds([
            [latitude, longitude],
            ...clinicians.slice(0, 12).map((clinician) => [clinician.lat, clinician.lon]),
        ], { padding: [28, 28], maxZoom: 14 });
    }

    function distanceKm(lat1, lon1, lat2, lon2) {
        const toRadians = (degrees) => degrees * Math.PI / 180;
        const earthRadiusKm = 6371;
        const dLat = toRadians(lat2 - lat1);
        const dLon = toRadians(lon2 - lon1);
        const a = Math.sin(dLat / 2) ** 2
            + Math.cos(toRadians(lat1)) * Math.cos(toRadians(lat2)) * Math.sin(dLon / 2) ** 2;

        return earthRadiusKm * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }
}
