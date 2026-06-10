const supportStorage = (() => {
    try {
        if (typeof window !== 'undefined' && window.localStorage) {
            return window.localStorage;
        }
    } catch {
        // Some restricted browser modes expose storage but block access.
    }

    const memory = new Map();

    return {
        getItem: (key) => memory.get(key) ?? null,
        setItem: (key, value) => memory.set(key, String(value)),
        removeItem: (key) => memory.delete(key),
        clear: () => memory.clear(),
    };
})();

// Keep header dropdowns from staying open over the page.
document.addEventListener('click', (e) => {
    document.querySelectorAll('.lang-dropdown details, .tool-menu details').forEach((details) => {
        if (details.open && !details.contains(e.target)) {
            details.open = false;
        }
    });
});

document.querySelectorAll('.lang-dropdown details, .tool-menu details').forEach((details) => {
    details.addEventListener('toggle', () => {
        if (! details.open) {
            return;
        }

        document.querySelectorAll('.lang-dropdown details, .tool-menu details').forEach((other) => {
            if (other !== details) {
                other.open = false;
            }
        });
    });

    details.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') {
            return;
        }

        details.open = false;
        details.querySelector('summary')?.focus();
    });
});

const root = document.querySelector('.shell');

if (root) {
    const sections = JSON.parse(root.dataset.sections);
    const ui = JSON.parse(root.dataset.ui);
    const activeLocale = root.dataset.locale || document.documentElement.lang || 'en';
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
    const printResultButton = document.getElementById('printResultButton');
    const downloadResultButton = document.getElementById('downloadResultButton');
    const clinicianMapPanel = document.getElementById('clinicianMapPanel');
    const clinicianStatus = document.getElementById('clinicianStatus');
    const clinicianList = document.getElementById('clinicianList');
    const fallbackMapLink = document.getElementById('fallbackMapLink');
    let clinicianMap = null;
    let currentResult = null;

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
        result.locale = activeLocale;
        currentResult = result;
        supportStorage.setItem('adhdSupport.latestResult', JSON.stringify(result));
        supportStorage.setItem(`adhdSupport.latestResult.${activeLocale}`, JSON.stringify(result));
        document.querySelector('.assessment-layout').hidden = true;
        resultsPanel.hidden = false;
        document.getElementById('resultTitle').textContent = result.title;
        document.getElementById('resultSummary').textContent = result.summary;
        document.getElementById('resultDisclaimer').textContent = result.disclaimer;
        document.getElementById('inattentiveCount').textContent = ui.count_of_total
            .replace(':count', result.counts.inattentive)
            .replace(':total', 9);
        document.getElementById('hyperactiveCount').textContent = ui.count_of_total
            .replace(':count', result.counts.hyperactive)
            .replace(':total', 9);
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

    printResultButton.addEventListener('click', () => {
        window.print();
    });

    downloadResultButton.addEventListener('click', () => {
        if (! currentResult) {
            return;
        }

        downloadText('adhd-screening-result.txt', resultToText(currentResult));
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

    function resultToText(result) {
        return [
            result.title,
            '',
            result.summary,
            '',
            `${ui.inattentive_signs}: ${result.counts.inattentive}/9`,
            `${ui.hyperactive_signs}: ${result.counts.hyperactive}/9`,
            `${ui.threshold_used}: ${result.threshold}+`,
            '',
            result.profile.plain_title,
            result.profile.plain,
            result.profile.not_character,
            '',
            result.profile.handles_title,
            ...result.profile.handles.map((item) => `- ${item}`),
            '',
            ui.next_steps,
            ...result.next_steps.map((item) => `- ${item}`),
            '',
            result.disclaimer,
        ].join('\n');
    }
}

const toolRoot = document.querySelector('.tool-shell');

if (toolRoot) {
    const tools = JSON.parse(toolRoot.dataset.tools);
    const tool = toolRoot.dataset.tool;
    const activeToolLocale = toolRoot.dataset.locale || document.documentElement.lang || 'en';

    if (tool === 'goal') {
        initGoalBuilder(tools);
    }

    if (tool === 'navigator') {
        initSupportNavigator(tools, JSON.parse(toolRoot.dataset.links || '{}'));
    }

    if (tool === 'planner') {
        initDailyPlanner(tools);
    }

    if (tool === 'time') {
        initTimeBlock(tools);
    }

    if (tool === 'body') {
        initBodyNeeds(tools);
    }

    if (tool === 'meds') {
        initMedicationRefill(tools);
    }

    if (tool === 'food') {
        initFoodRescue(tools);
    }

    if (tool === 'sleep') {
        initSleepWindDown(tools);
    }

    if (tool === 'motivation') {
        initMotivationMenu(tools);
    }

    if (tool === 'accountability') {
        initAccountability(tools);
    }

    if (tool === 'routine') {
        initRoutineBuilder(tools);
    }

    if (tool === 'weekly') {
        initWeeklyReset(tools);
    }

    if (tool === 'communication') {
        initCommunicationRepair(tools);
    }

    if (tool === 'energy') {
        initEnergyCrash(tools);
    }

    if (tool === 'decision') {
        initDecisionPriority(tools);
    }

    if (tool === 'focus') {
        initFocusSprint(tools);
    }

    if (tool === 'emotion') {
        initEmotionalReset(tools);
    }

    if (tool === 'transition') {
        initTransitionRescue(tools);
    }

    if (tool === 'appointment') {
        initAppointmentPrep(tools);
    }

    if (tool === 'care') {
        initCareNotes(tools);
    }

    if (tool === 'support') {
        initSupportRequest(tools);
    }

    if (tool === 'workschool') {
        initWorkSchoolSupport(tools);
    }

    if (tool === 'home') {
        initHomeReset(tools);
    }

    if (tool === 'laundry') {
        initLaundryRescue(tools);
    }

    if (tool === 'digital') {
        initDigitalClutter(tools);
    }

    if (tool === 'money') {
        initMoneyAdmin(tools);
    }

    if (tool === 'lost') {
        initLostItem(tools);
    }

    if (tool === 'errand') {
        initErrandLaunch(tools);
    }

    if (tool === 'providers') {
        initProviderShortlist(tools);
    }

    if (tool === 'access') {
        initAccessPlan(tools);
    }

    if (tool === 'task') {
        initTaskBreakdown(tools);
    }

    if (tool === 'dashboard') {
        initDashboard(tools, activeToolLocale);
    }

    if (tool === 'reminders') {
        initReminders(tools);
    }

    if (tool === 'tracker') {
        initTracker(tools);
    }
}

function initGoalBuilder(tools) {
    const form = document.getElementById('goalForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.goal') ?? '{}');
    const fields = {
        goal: document.getElementById('goalText'),
        why: document.getElementById('goalWhy'),
        blocker: document.getElementById('goalBlocker'),
        energy: document.getElementById('goalEnergy'),
    };

    hydrateFields(fields, saved);
    renderGoal(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.goal', JSON.stringify(data));
        renderGoal(tools, data);
    });

    document.getElementById('goalPrintButton').addEventListener('click', () => window.print());
    document.getElementById('goalDownloadButton').addEventListener('click', () => {
        const data = collectFields(fields);
        downloadText('adhd-goal-card.txt', goalToText(tools, data));
    });
    document.getElementById('goalClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.goal');
        hydrateFields(fields, {});
        renderGoal(tools, {});
    });
}

function renderGoal(tools, data) {
    const copy = tools.goal;
    const goal = data.goal?.trim();
    const blocker = data.blocker?.trim();
    const why = data.why?.trim();
    const energy = data.energy || 'low';
    const microsteps = buildMicrosteps(goal, energy);

    document.getElementById('goalOutputTitle').textContent = goal || copy.empty_title;
    document.getElementById('goalFirstStep').textContent = goal ? microsteps[0] : copy.empty_copy;
    document.getElementById('goalMicrosteps').innerHTML = goal
        ? microsteps.map((step) => `<li>${step}</li>`).join('')
        : '';
    document.getElementById('goalSupport').textContent = blocker
        ? `${copy.defaults.support} ${copy.labels.blocker}: ${blocker}.`
        : copy.defaults.support;
    document.getElementById('goalDone').textContent = why
        ? `${copy.defaults.done} ${copy.labels.why}: ${why}.`
        : copy.defaults.done;
}

function buildMicrosteps(goal, energy) {
    if (! goal) {
        return [];
    }

    const base = [
        `Write this on a visible note: ${goal}.`,
        'Set a 5-minute timer.',
        'Do the smallest visible part without trying to finish.',
        'Stop and decide the next tiny action.',
    ];

    if (energy === 'medium') {
        base.splice(2, 0, 'Clear one small space or open the needed app/document.');
    }

    if (energy === 'urgent') {
        base.splice(1, 0, 'Choose the version that is good enough, not perfect.');
        base.push('Send, submit, ask, or schedule the next handoff.');
    }

    return base;
}

function goalToText(tools, data) {
    const copy = tools.goal;
    const goal = data.goal?.trim() || copy.empty_title;
    const steps = buildMicrosteps(goal, data.energy || 'low');

    return [
        copy.output_eyebrow,
        goal,
        '',
        copy.sections.first_step,
        steps[0] ?? copy.defaults.first_step,
        '',
        copy.sections.microsteps,
        ...steps.map((step) => `- ${step}`),
        '',
        copy.sections.support,
        data.blocker ? `${copy.defaults.support} ${copy.labels.blocker}: ${data.blocker}` : copy.defaults.support,
        '',
        copy.sections.done,
        data.why ? `${copy.defaults.done} ${copy.labels.why}: ${data.why}` : copy.defaults.done,
    ].join('\n');
}

function initDailyPlanner(tools) {
    const form = document.getElementById('plannerForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.planner') ?? '{}');
    const fields = {
        must: document.getElementById('planMust'),
        should: document.getElementById('planShould'),
        body: document.getElementById('planBody'),
        scary: document.getElementById('planScary'),
        recovery: document.getElementById('planRecovery'),
    };

    hydrateFields(fields, saved);
    renderPlanner(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.planner', JSON.stringify(data));
        renderPlanner(tools, data);
    });

    document.getElementById('plannerPrintButton').addEventListener('click', () => window.print());
    document.getElementById('plannerDownloadButton').addEventListener('click', () => {
        downloadText('adhd-daily-plan.txt', plannerToText(tools, collectFields(fields)));
    });
    document.getElementById('plannerClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.planner');
        hydrateFields(fields, {});
        renderPlanner(tools, {});
    });
}

function renderPlanner(tools, data) {
    const copy = tools.planner;

    renderList('plannerMustList', lines(data.must), copy.defaults.must);
    renderList('plannerShouldList', lines(data.should), copy.defaults.should);
    document.getElementById('plannerBodyText').textContent = data.body || copy.defaults.body;
    document.getElementById('plannerScaryText').textContent = data.scary || copy.defaults.scary;
    document.getElementById('plannerRecoveryText').textContent = data.recovery || copy.defaults.recovery;
}

function plannerToText(tools, data) {
    const copy = tools.planner;

    return [
        copy.output_title,
        '',
        copy.sections.must,
        ...withDefault(lines(data.must), copy.defaults.must).map((item) => `- ${item}`),
        '',
        copy.sections.should,
        ...withDefault(lines(data.should), copy.defaults.should).map((item) => `- ${item}`),
        '',
        `${copy.sections.body}: ${data.body || copy.defaults.body}`,
        `${copy.sections.scary}: ${data.scary || copy.defaults.scary}`,
        `${copy.sections.recovery}: ${data.recovery || copy.defaults.recovery}`,
    ].join('\n');
}

function initTimeBlock(tools) {
    const form = document.getElementById('timeForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.time') ?? '{}');
    const defaults = tools.time.defaults;
    const fields = {
        start: document.getElementById('timeStart'),
        end: document.getElementById('timeEnd'),
        must: document.getElementById('timeMust'),
        fixed: document.getElementById('timeFixed'),
        buffer: document.getElementById('timeBuffer'),
        energy: document.getElementById('timeEnergy'),
        recovery: document.getElementById('timeRecovery'),
    };

    hydrateFields(fields, {
        start: defaults.start,
        end: defaults.end,
        buffer: defaults.buffer,
        energy: defaults.energy,
        ...saved,
    });
    renderTimeBlock(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.time', JSON.stringify(data));
        renderTimeBlock(tools, data);
    });

    document.getElementById('timePrintButton').addEventListener('click', () => window.print());
    document.getElementById('timeDownloadButton').addEventListener('click', () => {
        downloadText('adhd-time-block-plan.txt', timeBlockToText(tools, collectFields(fields)));
    });
    document.getElementById('timeClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.time');
        hydrateFields(fields, defaults);
        renderTimeBlock(tools, collectFields(fields));
    });
}

function renderTimeBlock(tools, data) {
    const plan = timeBlockPlan(tools.time, data);

    document.getElementById('timeOutputTitle').textContent = plan.title;
    document.getElementById('timeBlocksList').innerHTML = plan.blocks.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('timeFixedList').innerHTML = plan.fixed.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('timeBufferList').innerHTML = plan.buffers.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('timeRecoveryText').textContent = plan.recovery;
    document.getElementById('timeFallbackText').textContent = plan.fallback;
}

function timeBlockPlan(copy, data) {
    const start = parseClock(data.start) ?? parseClock(copy.defaults.start);
    let end = parseClock(data.end) ?? parseClock(copy.defaults.end);
    const buffer = copy.buffer_minutes[data.buffer] ?? copy.buffer_minutes.medium;
    const focus = copy.focus_minutes[data.energy] ?? copy.focus_minutes.medium;
    const tasks = lines(data.must).slice(0, 5);
    const fixed = withDefault(lines(data.fixed), copy.defaults.fixed);
    const recovery = data.recovery?.trim() || copy.defaults.recovery;

    if (end <= start) {
        end = start + 240;
    }

    let cursor = start;
    const blocks = [];

    (tasks.length ? tasks : [copy.defaults.must]).forEach((task) => {
        if (cursor >= end) {
            return;
        }

        const blockEnd = Math.min(cursor + focus, end);
        blocks.push(`${formatClock(cursor)} - ${formatClock(blockEnd)}: ${task}`);
        cursor = Math.min(blockEnd + buffer, end);
    });

    return {
        title: copy.output_title
            .replace(':start', formatClock(start))
            .replace(':end', formatClock(end)),
        blocks,
        fixed,
        buffers: [
            copy.buffer_rules.after.replace(':minutes', String(buffer)),
            copy.buffer_rules.before_fixed,
            copy.buffer_rules.blank,
        ],
        recovery: copy.recovery_block.replace(':recovery', recovery),
        fallback: copy.fallback_rule,
    };
}

function timeBlockToText(tools, data) {
    const copy = tools.time;
    const plan = timeBlockPlan(copy, data);

    return [
        plan.title,
        '',
        copy.sections.blocks,
        ...plan.blocks.map((item) => `- ${item}`),
        '',
        copy.sections.fixed,
        ...plan.fixed.map((item) => `- ${item}`),
        '',
        copy.sections.buffers,
        ...plan.buffers.map((item) => `- ${item}`),
        '',
        copy.sections.recovery,
        plan.recovery,
        '',
        copy.sections.fallback,
        plan.fallback,
    ].join('\n');
}

function initBodyNeeds(tools) {
    const form = document.getElementById('bodyForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.body') ?? '{}');
    const fields = {
        food: document.getElementById('bodyFood'),
        water: document.getElementById('bodyWater'),
        meds: document.getElementById('bodyMeds'),
        sleep: document.getElementById('bodySleep'),
        signal: document.getElementById('bodySignal'),
        task: document.getElementById('bodyTask'),
    };

    hydrateFields(fields, {
        food: 'unknown',
        water: 'unknown',
        meds: 'unknown',
        sleep: 'unknown',
        signal: 'unknown',
        ...saved,
    });
    renderBodyNeeds(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.body', JSON.stringify(data));
        renderBodyNeeds(tools, data);
    });

    document.getElementById('bodyPrintButton').addEventListener('click', () => window.print());
    document.getElementById('bodyDownloadButton').addEventListener('click', () => {
        downloadText('adhd-body-needs-checkin.txt', bodyNeedsToText(tools, collectFields(fields)));
    });
    document.getElementById('bodyClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.body');
        hydrateFields(fields, {
            food: 'unknown',
            water: 'unknown',
            meds: 'unknown',
            sleep: 'unknown',
            signal: 'unknown',
        });
        renderBodyNeeds(tools, collectFields(fields));
    });
}

function renderBodyNeeds(tools, data) {
    const plan = bodyNeedsPlan(tools.body, data);

    document.getElementById('bodyOutputTitle').textContent = tools.body.output_title;
    document.getElementById('bodyFirstText').textContent = plan.first;
    document.getElementById('bodyStepsList').innerHTML = plan.steps.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('bodyChecksList').innerHTML = plan.checks.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('bodyTaskText').textContent = plan.task;
    document.getElementById('bodyStopText').textContent = plan.stop;
}

function bodyNeedsPlan(copy, data) {
    const food = data.food || 'unknown';
    const water = data.water || 'unknown';
    const meds = data.meds || 'unknown';
    const sleep = data.sleep || 'unknown';
    const signal = data.signal || 'unknown';
    const task = data.task?.trim() || copy.defaults.task;

    return {
        first: copy.first_actions[food] || copy.first_actions.unknown,
        steps: [
            copy.water_steps[water] || copy.water_steps.unknown,
            copy.meds_steps[meds] || copy.meds_steps.unknown,
            copy.sleep_steps[sleep] || copy.sleep_steps.unknown,
            copy.signal_steps[signal] || copy.signal_steps.unknown,
        ],
        checks: copy.checks,
        task: copy.task_adjustment.replace(':task', task),
        stop: copy.stop_rule,
    };
}

function bodyNeedsToText(tools, data) {
    const copy = tools.body;
    const plan = bodyNeedsPlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.checks,
        ...plan.checks.map((item) => `- ${item}`),
        '',
        copy.sections.task,
        plan.task,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initMedicationRefill(tools) {
    const form = document.getElementById('medsForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.meds') ?? '{}');
    const fields = {
        mode: document.getElementById('medsMode'),
        medicine: document.getElementById('medsMedicine'),
        supply: document.getElementById('medsSupply'),
        blocker: document.getElementById('medsBlocker'),
        contact: document.getElementById('medsContact'),
        deadline: document.getElementById('medsDeadline'),
    };

    hydrateFields(fields, { mode: 'running_low', ...saved });
    renderMedicationRefill(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.meds', JSON.stringify(data));
        renderMedicationRefill(tools, data);
    });

    document.getElementById('medsPrintButton').addEventListener('click', () => window.print());
    document.getElementById('medsDownloadButton').addEventListener('click', () => {
        downloadText('adhd-medication-refill-rescue.txt', medicationRefillToText(tools, collectFields(fields)));
    });
    document.getElementById('medsClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.meds');
        hydrateFields(fields, { mode: 'running_low' });
        renderMedicationRefill(tools, collectFields(fields));
    });
}

function renderMedicationRefill(tools, data) {
    const copy = tools.meds;
    const plan = medicationRefillPlan(copy, data);

    document.getElementById('medsOutputTitle').textContent = copy.output_title;
    document.getElementById('medsFirstText').textContent = plan.first;
    document.getElementById('medsStepsList').innerHTML = plan.steps
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('medsScriptText').textContent = plan.script;
    renderList('medsQuestionsList', plan.questions, copy.questions[0]);
    document.getElementById('medsSafetyText').textContent = copy.safety_note;
    document.getElementById('medsStopText').textContent = copy.stop_rule;
}

function medicationRefillPlan(copy, data) {
    const mode = data.mode || 'running_low';
    const medicine = data.medicine?.trim() || copy.defaults.medicine;
    const supply = data.supply?.trim() || copy.defaults.supply;
    const contact = data.contact?.trim() || copy.defaults.contact;
    const deadline = data.deadline?.trim() || copy.defaults.deadline;
    const blocker = data.blocker?.trim() || '';
    const steps = [...(copy.mode_steps[mode] || copy.mode_steps.running_low)];

    if (blocker) {
        steps.push(copy.blocker_line.replace(':blocker', blocker));
    }

    return {
        first: copy.first_action
            .replace(':medicine', medicine)
            .replace(':supply', supply),
        steps,
        script: copy.script
            .replace(':medicine', medicine)
            .replace(':supply', supply)
            .replace(':contact', contact),
        questions: copy.questions.map((question) => question.replace(':deadline', deadline)),
    };
}

function medicationRefillToText(tools, data) {
    const copy = tools.meds;
    const plan = medicationRefillPlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.script,
        plan.script,
        '',
        copy.sections.questions,
        ...plan.questions.map((item) => `- ${item}`),
        '',
        copy.sections.safety,
        copy.safety_note,
        '',
        copy.sections.stop,
        copy.stop_rule,
    ].join('\n');
}

function initFoodRescue(tools) {
    const form = document.getElementById('foodForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.food') ?? '{}');
    const fields = {
        energy: document.getElementById('foodEnergy'),
        appetite: document.getElementById('foodAppetite'),
        kitchen: document.getElementById('foodKitchen'),
        budget: document.getElementById('foodBudget'),
        available: document.getElementById('foodAvailable'),
        nextThing: document.getElementById('foodNextThing'),
    };

    hydrateFields(fields, {
        energy: 'low',
        appetite: 'forgot',
        kitchen: 'microwave',
        budget: 'very_low',
        ...saved,
    });
    renderFoodRescue(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.food', JSON.stringify(data));
        renderFoodRescue(tools, data);
    });

    document.getElementById('foodPrintButton').addEventListener('click', () => window.print());
    document.getElementById('foodDownloadButton').addEventListener('click', () => {
        downloadText('adhd-food-rescue.txt', foodRescueToText(tools, collectFields(fields)));
    });
    document.getElementById('foodClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.food');
        hydrateFields(fields, {
            energy: 'low',
            appetite: 'forgot',
            kitchen: 'microwave',
            budget: 'very_low',
        });
        renderFoodRescue(tools, collectFields(fields));
    });
}

function renderFoodRescue(tools, data) {
    const plan = foodRescuePlan(tools.food, data);

    document.getElementById('foodOutputTitle').textContent = tools.food.output_title;
    document.getElementById('foodFirstText').textContent = plan.first;
    document.getElementById('foodOptionsList').innerHTML = plan.options.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('foodShoppingList').innerHTML = plan.shopping.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('foodNextText').textContent = plan.next;
    document.getElementById('foodRulesList').innerHTML = plan.rules.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('foodStopText').textContent = plan.stop;
}

function foodRescuePlan(copy, data) {
    const energy = copy.options[data.energy] ? data.energy : 'low';
    const appetite = copy.appetite_lines[data.appetite] ? data.appetite : 'unsure';
    const kitchen = copy.kitchen_lines[data.kitchen] ? data.kitchen : 'unsure';
    const budget = copy.shopping[data.budget] ? data.budget : 'very_low';
    const available = data.available?.trim() || copy.defaults.available;
    const nextThing = data.nextThing?.trim() || copy.defaults.next_thing;

    return {
        first: copy.first_action
            .replace(':appetite', copy.appetite_lines[appetite])
            .replace(':kitchen', copy.kitchen_lines[kitchen])
            .replace(':available', available),
        options: copy.options[energy],
        shopping: copy.shopping[budget],
        next: copy.next_cue.replace(':next_thing', nextThing),
        rules: copy.rules,
        stop: copy.stop_rule,
    };
}

function foodRescueToText(tools, data) {
    const copy = tools.food;
    const plan = foodRescuePlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.options,
        ...plan.options.map((item) => `- ${item}`),
        '',
        copy.sections.shopping,
        ...plan.shopping.map((item) => `- ${item}`),
        '',
        copy.sections.next,
        plan.next,
        '',
        copy.sections.rules,
        ...plan.rules.map((item) => `- ${item}`),
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initSleepWindDown(tools) {
    const form = document.getElementById('sleepForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.sleep') ?? '{}');
    const fields = {
        mode: document.getElementById('sleepMode'),
        wakeTime: document.getElementById('sleepWakeTime'),
        blocker: document.getElementById('sleepBlocker'),
        tomorrow: document.getElementById('sleepTomorrow'),
        screenRule: document.getElementById('sleepScreenRule'),
        comfort: document.getElementById('sleepComfort'),
    };

    hydrateFields(fields, {
        mode: 'late',
        ...saved,
    });
    renderSleepWindDown(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.sleep', JSON.stringify(data));
        renderSleepWindDown(tools, data);
    });

    document.getElementById('sleepPrintButton').addEventListener('click', () => window.print());
    document.getElementById('sleepDownloadButton').addEventListener('click', () => {
        downloadText('adhd-sleep-wind-down.txt', sleepWindDownToText(tools, collectFields(fields)));
    });
    document.getElementById('sleepClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.sleep');
        hydrateFields(fields, { mode: 'late' });
        renderSleepWindDown(tools, collectFields(fields));
    });
}

function renderSleepWindDown(tools, data) {
    const plan = sleepWindDownPlan(tools.sleep, data);

    document.getElementById('sleepOutputTitle').textContent = tools.sleep.output_title;
    document.getElementById('sleepFirstText').textContent = plan.first;
    document.getElementById('sleepStepsList').innerHTML = plan.steps.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('sleepScreenText').textContent = plan.screen;
    document.getElementById('sleepTomorrowText').textContent = plan.tomorrow;
    document.getElementById('sleepRulesList').innerHTML = plan.rules.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('sleepStopText').textContent = plan.stop;
}

function sleepWindDownPlan(copy, data) {
    const mode = copy.steps[data.mode] ? data.mode : 'late';
    const wakeTime = data.wakeTime?.trim() || copy.defaults.wake_time;
    const blocker = data.blocker?.trim() || copy.defaults.blocker;
    const tomorrow = data.tomorrow?.trim() || copy.defaults.tomorrow;
    const screenRule = data.screenRule?.trim() || copy.defaults.screen_rule;
    const comfort = data.comfort?.trim() || copy.defaults.comfort;

    return {
        first: copy.first_action
            .replace(':blocker', blocker)
            .replace(':comfort', comfort),
        steps: copy.steps[mode],
        screen: copy.screen_boundary.replace(':screen_rule', screenRule),
        tomorrow: copy.tomorrow_launch
            .replace(':wake_time', wakeTime)
            .replace(':tomorrow', tomorrow),
        rules: copy.rules,
        stop: copy.stop_rule,
    };
}

function sleepWindDownToText(tools, data) {
    const copy = tools.sleep;
    const plan = sleepWindDownPlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.screen,
        plan.screen,
        '',
        copy.sections.tomorrow,
        plan.tomorrow,
        '',
        copy.sections.rules,
        ...plan.rules.map((item) => `- ${item}`),
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initMotivationMenu(tools) {
    const form = document.getElementById('motivationForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.motivation') ?? '{}');
    const fields = {
        task: document.getElementById('motivationTask'),
        mood: document.getElementById('motivationMood'),
        reward: document.getElementById('motivationReward'),
        stimulation: document.getElementById('motivationStimulation'),
        friction: document.getElementById('motivationFriction'),
        cost: document.getElementById('motivationCost'),
    };

    hydrateFields(fields, {
        reward: 'novelty',
        stimulation: 'music',
        ...saved,
    });
    renderMotivationMenu(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.motivation', JSON.stringify(data));
        renderMotivationMenu(tools, data);
    });

    document.getElementById('motivationPrintButton').addEventListener('click', () => window.print());
    document.getElementById('motivationDownloadButton').addEventListener('click', () => {
        downloadText('adhd-motivation-menu.txt', motivationToText(tools, collectFields(fields)));
    });
    document.getElementById('motivationClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.motivation');
        hydrateFields(fields, {
            reward: 'novelty',
            stimulation: 'music',
        });
        renderMotivationMenu(tools, collectFields(fields));
    });
}

function renderMotivationMenu(tools, data) {
    const plan = motivationPlan(tools.motivation, data);

    document.getElementById('motivationOutputTitle').textContent = tools.motivation.output_title;
    document.getElementById('motivationStarterText').textContent = plan.starter;
    document.getElementById('motivationMenuList').innerHTML = plan.menu.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('motivationPairingText').textContent = plan.pairing;
    document.getElementById('motivationFrictionText').textContent = plan.friction;
    document.getElementById('motivationRulesList').innerHTML = plan.rules.map((item) => `<li>${item}</li>`).join('');
}

function motivationPlan(copy, data) {
    const task = data.task?.trim() || copy.defaults.task;
    const mood = data.mood?.trim() || copy.defaults.mood;
    const reward = data.reward || 'novelty';
    const stimulation = data.stimulation || 'music';
    const friction = data.friction?.trim() || copy.defaults.friction;
    const cost = data.cost?.trim() || copy.defaults.cost;

    return {
        starter: copy.starter
            .replace(':task', task)
            .replace(':mood', mood),
        menu: copy.menus[reward] || copy.menus.novelty,
        pairing: copy.stimulation[stimulation] || copy.stimulation.music,
        friction: copy.friction_plan
            .replace(':friction', friction)
            .replace(':task', task)
            .replace(':cost', cost),
        rules: copy.rules,
    };
}

function motivationToText(tools, data) {
    const copy = tools.motivation;
    const plan = motivationPlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.starter,
        plan.starter,
        '',
        copy.sections.menu,
        ...plan.menu.map((item) => `- ${item}`),
        '',
        copy.sections.pairing,
        plan.pairing,
        '',
        copy.sections.friction,
        plan.friction,
        '',
        copy.sections.rules,
        ...plan.rules.map((item) => `- ${item}`),
    ].join('\n');
}

function initAccountability(tools) {
    const form = document.getElementById('accountabilityForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.accountability') ?? '{}');
    const fields = {
        task: document.getElementById('accountabilityTask'),
        person: document.getElementById('accountabilityPerson'),
        format: document.getElementById('accountabilityFormat'),
        time: document.getElementById('accountabilityTime'),
        proof: document.getElementById('accountabilityProof'),
        missed: document.getElementById('accountabilityMissed'),
    };

    hydrateFields(fields, {
        format: 'text',
        ...saved,
    });
    renderAccountability(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.accountability', JSON.stringify(data));
        renderAccountability(tools, data);
    });

    document.getElementById('accountabilityPrintButton').addEventListener('click', () => window.print());
    document.getElementById('accountabilityDownloadButton').addEventListener('click', () => {
        downloadText('adhd-accountability-checkin.txt', accountabilityToText(tools, collectFields(fields)));
    });
    document.getElementById('accountabilityClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.accountability');
        hydrateFields(fields, { format: 'text' });
        renderAccountability(tools, collectFields(fields));
    });
}

function renderAccountability(tools, data) {
    const plan = accountabilityPlan(tools.accountability, data);

    document.getElementById('accountabilityOutputTitle').textContent = tools.accountability.output_title;
    document.getElementById('accountabilityScriptText').textContent = plan.script;
    document.getElementById('accountabilityStepsList').innerHTML = plan.steps.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('accountabilityProofText').textContent = plan.proof;
    document.getElementById('accountabilityMissedText').textContent = plan.missed;
    document.getElementById('accountabilityRulesList').innerHTML = plan.rules.map((item) => `<li>${item}</li>`).join('');
}

function accountabilityPlan(copy, data) {
    const task = data.task?.trim() || copy.defaults.task;
    const person = data.person?.trim() || copy.defaults.person;
    const format = copy.formats_sentence[data.format] || copy.formats_sentence.text;
    const time = data.time?.trim() || copy.defaults.time;
    const proof = data.proof?.trim() || copy.defaults.proof;
    const missed = data.missed?.trim() || copy.defaults.missed;

    return {
        script: copy.script
            .replace(':person', person)
            .replace(':task', task)
            .replace(':time', time)
            .replace(':format', format),
        steps: [
            copy.steps.before.replace(':time', time),
            copy.steps.start
                .replace(':time', time)
                .replace(':task', task)
                .replace(':person', person),
            copy.steps.after.replace(':proof', proof),
        ],
        proof: copy.proof_line.replace(':proof', proof),
        missed: copy.missed_line.replace(':missed', missed),
        rules: copy.rules,
    };
}

function accountabilityToText(tools, data) {
    const copy = tools.accountability;
    const plan = accountabilityPlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.script,
        plan.script,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.proof,
        plan.proof,
        '',
        copy.sections.missed,
        plan.missed,
        '',
        copy.sections.rules,
        ...plan.rules.map((item) => `- ${item}`),
    ].join('\n');
}

function initRoutineBuilder(tools) {
    const form = document.getElementById('routineForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.routine') ?? '{}');
    const fields = {
        kind: document.getElementById('routineKind'),
        anchor: document.getElementById('routineAnchor'),
        must: document.getElementById('routineMust'),
        friction: document.getElementById('routineFriction'),
        fallback: document.getElementById('routineFallback'),
    };

    hydrateFields(fields, saved);
    renderRoutine(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.routine', JSON.stringify(data));
        renderRoutine(tools, data);
    });

    document.getElementById('routinePrintButton').addEventListener('click', () => window.print());
    document.getElementById('routineDownloadButton').addEventListener('click', () => {
        downloadText('adhd-routine-card.txt', routineToText(tools, collectFields(fields)));
    });
    document.getElementById('routineClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.routine');
        hydrateFields(fields, {});
        renderRoutine(tools, {});
    });
}

function renderRoutine(tools, data) {
    const copy = tools.routine;
    const kind = data.kind || 'morning';

    document.getElementById('routineOutputTitle').textContent = `${copy.output_title}: ${copy.kinds[kind] || copy.kinds.morning}`;
    document.getElementById('routineAnchorText').textContent = routineAnchor(copy, data, kind);
    document.getElementById('routineStepList').innerHTML = routineSteps(copy, data, kind)
        .map((step) => `<li>${step}</li>`)
        .join('');
    renderList('routinePrepList', routinePrep(copy, data, kind), copy.prep[kind][0]);
    document.getElementById('routineFallbackText').textContent = data.fallback?.trim() || copy.fallbacks[kind] || copy.fallbacks.morning;
    document.getElementById('routineResetText').textContent = data.friction?.trim()
        ? `${copy.reset} ${copy.friction_note.replace(':friction', data.friction.trim())}`
        : copy.reset;
}

function routineAnchor(copy, data, kind) {
    return data.anchor?.trim() || copy.anchors[kind] || copy.anchors.morning;
}

function routineSteps(copy, data, kind) {
    const custom = lines(data.must);

    if (custom.length > 0) {
        return [
            copy.start_step,
            ...custom.slice(0, 5),
            copy.end_step,
        ];
    }

    return copy.steps[kind] || copy.steps.morning;
}

function routinePrep(copy, data, kind) {
    const prep = [...(copy.prep[kind] || copy.prep.morning)];
    const friction = data.friction?.trim();

    if (friction) {
        prep.push(copy.prep_for_friction.replace(':friction', friction));
    }

    return prep;
}

function routineToText(tools, data) {
    const copy = tools.routine;
    const kind = data.kind || 'morning';

    return [
        `${copy.output_title}: ${copy.kinds[kind] || copy.kinds.morning}`,
        '',
        copy.sections.anchor,
        routineAnchor(copy, data, kind),
        '',
        copy.sections.steps,
        ...routineSteps(copy, data, kind).map((item) => `- ${item}`),
        '',
        copy.sections.prep,
        ...routinePrep(copy, data, kind).map((item) => `- ${item}`),
        '',
        copy.sections.fallback,
        data.fallback?.trim() || copy.fallbacks[kind] || copy.fallbacks.morning,
        '',
        copy.sections.reset,
        data.friction?.trim()
            ? `${copy.reset} ${copy.friction_note.replace(':friction', data.friction.trim())}`
            : copy.reset,
    ].join('\n');
}

function initHomeReset(tools) {
    const form = document.getElementById('homeForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.home') ?? '{}');
    const fields = {
        space: document.getElementById('homeSpace'),
        mode: document.getElementById('homeMode'),
        time: document.getElementById('homeTime'),
        blocker: document.getElementById('homeBlocker'),
        reward: document.getElementById('homeReward'),
    };

    hydrateFields(fields, saved);
    renderHomeReset(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.home', JSON.stringify(data));
        renderHomeReset(tools, data);
    });

    document.getElementById('homePrintButton').addEventListener('click', () => window.print());
    document.getElementById('homeDownloadButton').addEventListener('click', () => {
        downloadText('adhd-home-reset.txt', homeToText(tools, collectFields(fields)));
    });
    document.getElementById('homeClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.home');
        hydrateFields(fields, {});
        renderHomeReset(tools, {});
    });
}

function renderHomeReset(tools, data) {
    const copy = tools.home;
    const space = data.space?.trim() || copy.defaults.space;
    const mode = data.mode || 'low_energy';
    const minutes = Number(data.time || 10);

    document.getElementById('homeOutputTitle').textContent = copy.output_title.replace(':space', space);
    document.getElementById('homeStartText').textContent = homeStart(copy, space, minutes, data.blocker);
    document.getElementById('homeStepList').innerHTML = homeSteps(copy, mode, minutes)
        .map((step) => `<li>${step}</li>`)
        .join('');
    renderList('homeParkingList', copy.parking, copy.parking[0]);
    document.getElementById('homeStopText').textContent = data.reward?.trim()
        ? `${copy.stop_with_reward} ${data.reward.trim()}`
        : copy.stop;
    document.getElementById('homeKindText').textContent = copy.kind;
}

function homeStart(copy, space, minutes, blocker) {
    const start = copy.start
        .replace(':space', space)
        .replace(':minutes', minutes);

    return blocker?.trim()
        ? `${start} ${copy.blocker_note.replace(':blocker', blocker.trim())}`
        : start;
}

function homeSteps(copy, mode, minutes) {
    const steps = [...(copy.sequences[mode] || copy.sequences.low_energy)];

    if (minutes <= 5) {
        return steps.slice(0, 3);
    }

    if (minutes >= 20) {
        return [...steps, ...copy.extra_steps];
    }

    return steps;
}

function homeToText(tools, data) {
    const copy = tools.home;
    const space = data.space?.trim() || copy.defaults.space;
    const mode = data.mode || 'low_energy';
    const minutes = Number(data.time || 10);

    return [
        copy.output_title.replace(':space', space),
        '',
        copy.sections.start,
        homeStart(copy, space, minutes, data.blocker),
        '',
        copy.sections.steps,
        ...homeSteps(copy, mode, minutes).map((item) => `- ${item}`),
        '',
        copy.sections.parking,
        ...copy.parking.map((item) => `- ${item}`),
        '',
        copy.sections.stop,
        data.reward?.trim() ? `${copy.stop_with_reward} ${data.reward.trim()}` : copy.stop,
        '',
        copy.sections.kind,
        copy.kind,
    ].join('\n');
}

function initLaundryRescue(tools) {
    const form = document.getElementById('laundryForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.laundry') ?? '{}');
    const fields = {
        mode: document.getElementById('laundryMode'),
        needed: document.getElementById('laundryNeeded'),
        energy: document.getElementById('laundryEnergy'),
        machine: document.getElementById('laundryMachine'),
        blocker: document.getElementById('laundryBlocker'),
        deadline: document.getElementById('laundryDeadline'),
    };

    hydrateFields(fields, {
        mode: 'wearable',
        energy: 'low',
        machine: 'unknown',
        ...saved,
    });
    renderLaundryRescue(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.laundry', JSON.stringify(data));
        renderLaundryRescue(tools, data);
    });

    document.getElementById('laundryPrintButton').addEventListener('click', () => window.print());
    document.getElementById('laundryDownloadButton').addEventListener('click', () => {
        downloadText('adhd-laundry-rescue.txt', laundryRescueToText(tools, collectFields(fields)));
    });
    document.getElementById('laundryClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.laundry');
        hydrateFields(fields, { mode: 'wearable', energy: 'low', machine: 'unknown' });
        renderLaundryRescue(tools, collectFields(fields));
    });
}

function renderLaundryRescue(tools, data) {
    const copy = tools.laundry;
    const plan = laundryRescuePlan(copy, data);

    document.getElementById('laundryOutputTitle').textContent = copy.output_title;
    document.getElementById('laundryFirstText').textContent = plan.first;
    document.getElementById('laundryStepsList').innerHTML = plan.steps
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('laundryDryingText').textContent = plan.drying;
    document.getElementById('laundryEmergencyText').textContent = plan.emergency;
    renderList('laundryMinimumsList', plan.minimums, copy.minimums[0]);
    document.getElementById('laundryStopText').textContent = plan.stop;
}

function laundryRescuePlan(copy, data) {
    const mode = data.mode || 'wearable';
    const energy = data.energy || 'low';
    const machine = data.machine || 'unknown';
    const needed = data.needed?.trim() || copy.defaults.needed;
    const deadline = data.deadline?.trim() || copy.defaults.deadline;
    const blocker = data.blocker?.trim() || '';
    const steps = [
        ...(copy.mode_steps[mode] || copy.mode_steps.wearable),
        copy.machine_steps[machine] || copy.machine_steps.unknown,
        copy.energy_steps[energy] || copy.energy_steps.low,
    ];

    if (blocker) {
        steps.push(copy.blocker_line.replace(':blocker', blocker));
    }

    return {
        first: copy.first_action.replace(':needed', needed),
        steps,
        drying: copy.drying_plan.replace(':deadline', deadline),
        emergency: copy.emergency_outfit.replace(':deadline', deadline),
        minimums: copy.minimums,
        stop: copy.stop_rule,
    };
}

function laundryRescueToText(tools, data) {
    const copy = tools.laundry;
    const plan = laundryRescuePlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.drying,
        plan.drying,
        '',
        copy.sections.emergency,
        plan.emergency,
        '',
        copy.sections.minimums,
        ...plan.minimums.map((item) => `- ${item}`),
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initDigitalClutter(tools) {
    const form = document.getElementById('digitalForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.digital') ?? '{}');
    const fields = {
        mode: document.getElementById('digitalMode'),
        target: document.getElementById('digitalTarget'),
        energy: document.getElementById('digitalEnergy'),
        device: document.getElementById('digitalDevice'),
        blocker: document.getElementById('digitalBlocker'),
        deadline: document.getElementById('digitalDeadline'),
    };

    hydrateFields(fields, {
        mode: 'overwhelm',
        energy: 'low',
        device: 'unknown',
        ...saved,
    });
    renderDigitalClutter(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.digital', JSON.stringify(data));
        renderDigitalClutter(tools, data);
    });

    document.getElementById('digitalPrintButton').addEventListener('click', () => window.print());
    document.getElementById('digitalDownloadButton').addEventListener('click', () => {
        downloadText('adhd-digital-clutter-rescue.txt', digitalClutterToText(tools, collectFields(fields)));
    });
    document.getElementById('digitalClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.digital');
        hydrateFields(fields, { mode: 'overwhelm', energy: 'low', device: 'unknown' });
        renderDigitalClutter(tools, collectFields(fields));
    });
}

function renderDigitalClutter(tools, data) {
    const copy = tools.digital;
    const plan = digitalClutterPlan(copy, data);

    document.getElementById('digitalOutputTitle').textContent = copy.output_title;
    document.getElementById('digitalFirstText').textContent = plan.first;
    document.getElementById('digitalStepsList').innerHTML = plan.steps
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('digitalSearchText').textContent = plan.search;
    document.getElementById('digitalShutdownText').textContent = plan.shutdown;
    renderList('digitalParkingList', plan.parking, copy.parking_rules[0]);
    document.getElementById('digitalStopText').textContent = plan.stop;
}

function digitalClutterPlan(copy, data) {
    const mode = data.mode || 'overwhelm';
    const energy = data.energy || 'low';
    const device = data.device || 'unknown';
    const target = data.target?.trim() || copy.defaults.target;
    const deadline = data.deadline?.trim() || copy.defaults.deadline;
    const blocker = data.blocker?.trim() || '';
    const steps = [
        ...(copy.mode_steps[mode] || copy.mode_steps.overwhelm),
        copy.device_steps[device] || copy.device_steps.unknown,
        copy.energy_steps[energy] || copy.energy_steps.low,
    ];

    if (blocker) {
        steps.push(copy.blocker_line.replace(':blocker', blocker));
    }

    return {
        first: copy.first_action.replace(':target', target),
        steps,
        search: copy.search_rule,
        shutdown: copy.shutdown_rule.replace(':deadline', deadline),
        parking: copy.parking_rules,
        stop: copy.stop_rule,
    };
}

function digitalClutterToText(tools, data) {
    const copy = tools.digital;
    const plan = digitalClutterPlan(copy, data);

    return [
        copy.output_title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.search,
        plan.search,
        '',
        copy.sections.shutdown,
        plan.shutdown,
        '',
        copy.sections.parking,
        ...plan.parking.map((item) => `- ${item}`),
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initMoneyAdmin(tools) {
    const form = document.getElementById('moneyForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.money') ?? '{}');
    const fields = {
        task: document.getElementById('moneyTask'),
        category: document.getElementById('moneyCategory'),
        urgency: document.getElementById('moneyUrgency'),
        blocker: document.getElementById('moneyBlocker'),
        contact: document.getElementById('moneyContact'),
        outcome: document.getElementById('moneyOutcome'),
    };

    hydrateFields(fields, saved);
    renderMoneyAdmin(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.money', JSON.stringify(data));
        renderMoneyAdmin(tools, data);
    });

    document.getElementById('moneyPrintButton').addEventListener('click', () => window.print());
    document.getElementById('moneyDownloadButton').addEventListener('click', () => {
        downloadText('adhd-money-admin-rescue.txt', moneyToText(tools, collectFields(fields)));
    });
    document.getElementById('moneyClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.money');
        hydrateFields(fields, {});
        renderMoneyAdmin(tools, {});
    });
}

function renderMoneyAdmin(tools, data) {
    const copy = tools.money;
    const category = data.category || 'overdue';
    const task = data.task?.trim() || copy.defaults.task[category] || copy.defaults.task.overdue;
    const outcome = data.outcome?.trim() || copy.defaults.outcome;

    document.getElementById('moneyOutputTitle').textContent = copy.output_title.replace(':task', task);
    document.getElementById('moneyFirstText').textContent = moneyFirstAction(copy, task, category, data.urgency, data.blocker);
    document.getElementById('moneyStepList').innerHTML = moneySteps(copy, category)
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('moneyScriptText').textContent = moneyScript(copy, category, data.contact, task);
    renderList('moneyQuestionList', moneyQuestions(copy, category, outcome), copy.questions.shared[0]);
    document.getElementById('moneyStopText').textContent = copy.stop_rule.replace(':outcome', outcome);
}

function moneyFirstAction(copy, task, category, urgency, blocker) {
    const urgencyText = copy.urgency_notes[urgency || 'unsure'] || copy.urgency_notes.unsure;
    const blockerText = blocker?.trim()
        ? ` ${copy.blocker_note.replace(':blocker', blocker.trim())}`
        : '';

    return `${copy.first_action.replace(':task', task).replace(':category', copy.categories[category] || copy.categories.overdue)} ${urgencyText}${blockerText}`;
}

function moneySteps(copy, category) {
    return copy.steps[category] || copy.steps.overdue;
}

function moneyScript(copy, category, contact, task) {
    const person = contact?.trim() || copy.defaults.contact[category] || copy.defaults.contact.overdue;
    const template = copy.scripts[category] || copy.scripts.overdue;

    return template
        .replace(':contact', person)
        .replace(':task', task);
}

function moneyQuestions(copy, category, outcome) {
    return [
        ...(copy.questions[category] || copy.questions.overdue),
        ...copy.questions.shared,
        copy.outcome_question.replace(':outcome', outcome),
    ];
}

function moneyToText(tools, data) {
    const copy = tools.money;
    const category = data.category || 'overdue';
    const task = data.task?.trim() || copy.defaults.task[category] || copy.defaults.task.overdue;
    const outcome = data.outcome?.trim() || copy.defaults.outcome;

    return [
        copy.output_title.replace(':task', task),
        '',
        copy.sections.first,
        moneyFirstAction(copy, task, category, data.urgency, data.blocker),
        '',
        copy.sections.steps,
        ...moneySteps(copy, category).map((item) => `- ${item}`),
        '',
        copy.sections.script,
        moneyScript(copy, category, data.contact, task),
        '',
        copy.sections.questions,
        ...moneyQuestions(copy, category, outcome).map((item) => `- ${item}`),
        '',
        copy.sections.stop,
        copy.stop_rule.replace(':outcome', outcome),
    ].join('\n');
}

function initLostItem(tools) {
    const form = document.getElementById('lostForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.lost') ?? '{}');
    const fields = {
        item: document.getElementById('lostItem'),
        type: document.getElementById('lostType'),
        urgency: document.getElementById('lostUrgency'),
        lastSeen: document.getElementById('lostLastSeen'),
        searchArea: document.getElementById('lostSearchArea'),
        landingSpot: document.getElementById('lostLandingSpot'),
    };

    hydrateFields(fields, {
        type: 'keys',
        urgency: 'calm',
        ...saved,
    });
    renderLostItem(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.lost', JSON.stringify(data));
        renderLostItem(tools, data);
    });

    document.getElementById('lostPrintButton').addEventListener('click', () => window.print());
    document.getElementById('lostDownloadButton').addEventListener('click', () => {
        downloadText('adhd-lost-item-rescue.txt', lostItemToText(tools, collectFields(fields)));
    });
    document.getElementById('lostClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.lost');
        hydrateFields(fields, { type: 'keys', urgency: 'calm' });
        renderLostItem(tools, collectFields(fields));
    });
}

function renderLostItem(tools, data) {
    const plan = lostItemPlan(tools.lost, data);

    document.getElementById('lostOutputTitle').textContent = tools.lost.output_title.replace(':item', plan.item);
    document.getElementById('lostFirstText').textContent = plan.first;
    document.getElementById('lostSearchList').innerHTML = plan.search.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('lostBackupList').innerHTML = plan.backup.map((item) => `<li>${item}</li>`).join('');
    document.getElementById('lostPreventionText').textContent = plan.prevention;
    document.getElementById('lostStopText').textContent = plan.stop;
}

function lostItemPlan(copy, data) {
    const item = data.item?.trim() || copy.defaults.item;
    const type = copy.type_hints[data.type] ? data.type : 'other';
    const urgency = copy.urgency_lines[data.urgency] ? data.urgency : 'calm';
    const lastSeen = data.lastSeen?.trim() || copy.defaults.last_seen;
    const searchArea = data.searchArea?.trim() || copy.defaults.search_area;
    const landingSpot = data.landingSpot?.trim() || copy.defaults.landing_spot;

    return {
        item,
        first: copy.first_action.replace(':item', item),
        search: [
            copy.search_steps.area.replace(':search_area', searchArea),
            copy.search_steps.path.replace(':last_seen', lastSeen),
            copy.search_steps.type.replace(':hint', copy.type_hints[type]),
            copy.search_steps.odd,
            copy.urgency_lines[urgency],
        ],
        backup: copy.backup_steps,
        prevention: copy.prevention_rule
            .replace(':item', item)
            .replace(':landing_spot', landingSpot),
        stop: copy.stop_rule,
    };
}

function lostItemToText(tools, data) {
    const copy = tools.lost;
    const plan = lostItemPlan(copy, data);

    return [
        copy.output_title.replace(':item', plan.item),
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.search,
        ...plan.search.map((item) => `- ${item}`),
        '',
        copy.sections.backup,
        ...plan.backup.map((item) => `- ${item}`),
        '',
        copy.sections.prevention,
        plan.prevention,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initErrandLaunch(tools) {
    const form = document.getElementById('errandForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.errand') ?? '{}');
    const fields = {
        destination: document.getElementById('errandDestination'),
        kind: document.getElementById('errandKind'),
        travel: document.getElementById('errandTravel'),
        deadline: document.getElementById('errandDeadline'),
        blocker: document.getElementById('errandBlocker'),
        bring: document.getElementById('errandBring'),
    };

    hydrateFields(fields, {
        kind: 'appointment',
        travel: 'unsure',
        ...saved,
    });
    renderErrandLaunch(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.errand', JSON.stringify(data));
        renderErrandLaunch(tools, data);
    });

    document.getElementById('errandPrintButton').addEventListener('click', () => window.print());
    document.getElementById('errandDownloadButton').addEventListener('click', () => {
        downloadText('adhd-errand-launch.txt', errandLaunchToText(tools, collectFields(fields)));
    });
    document.getElementById('errandClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.errand');
        hydrateFields(fields, { kind: 'appointment', travel: 'unsure' });
        renderErrandLaunch(tools, collectFields(fields));
    });
}

function renderErrandLaunch(tools, data) {
    const plan = errandLaunchPlan(tools.errand, data);

    document.getElementById('errandOutputTitle').textContent = tools.errand.output_title.replace(':destination', plan.destination);
    document.getElementById('errandFirstText').textContent = plan.first;
    renderList('errandStepList', plan.steps, tools.errand.defaults.step);
    renderList('errandBringList', plan.bring, tools.errand.defaults.bring_item);
    document.getElementById('errandLateText').textContent = plan.late;
    renderList('errandBackupList', plan.backup, tools.errand.defaults.backup);
    document.getElementById('errandStopText').textContent = plan.stop;
}

function errandLaunchPlan(copy, data) {
    const destination = data.destination?.trim() || copy.defaults.destination;
    const kind = copy.kind_steps[data.kind] ? data.kind : 'unsure';
    const travel = copy.travel_steps[data.travel] ? data.travel : 'unsure';
    const deadline = data.deadline?.trim() || copy.defaults.deadline;
    const bring = withDefault(lines(data.bring), copy.defaults.bring_item);
    const steps = [
        copy.deadline_step.replace(':deadline', deadline),
        copy.travel_steps[travel],
        ...copy.kind_steps[kind],
    ];

    if (data.blocker?.trim()) {
        steps.push(copy.blocker_step.replace(':blocker', data.blocker.trim()));
    }

    return {
        destination,
        first: copy.first_action.replace(':destination', destination),
        steps,
        bring,
        late: copy.late_script.replace(':destination', destination),
        backup: copy.backup_steps,
        stop: copy.stop_rule,
    };
}

function errandLaunchToText(tools, data) {
    const copy = tools.errand;
    const plan = errandLaunchPlan(copy, data);

    return [
        copy.output_title.replace(':destination', plan.destination),
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.bring,
        ...plan.bring.map((item) => `- ${item}`),
        '',
        copy.sections.late,
        plan.late,
        '',
        copy.sections.backup,
        ...plan.backup.map((item) => `- ${item}`),
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initWeeklyReset(tools) {
    const form = document.getElementById('weeklyForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.weekly') ?? '{}');
    const fields = {
        wins: document.getElementById('weeklyWins'),
        loose: document.getElementById('weeklyLoose'),
        must: document.getElementById('weeklyMust'),
        support: document.getElementById('weeklySupport'),
        reset: document.getElementById('weeklyResetMode'),
    };

    hydrateFields(fields, saved);
    renderWeeklyReset(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.weekly', JSON.stringify(data));
        renderWeeklyReset(tools, data);
    });

    document.getElementById('weeklyPrintButton').addEventListener('click', () => window.print());
    document.getElementById('weeklyDownloadButton').addEventListener('click', () => {
        downloadText('adhd-weekly-reset.txt', weeklyToText(tools, collectFields(fields)));
    });
    document.getElementById('weeklyClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.weekly');
        hydrateFields(fields, {});
        renderWeeklyReset(tools, {});
    });
}

function renderWeeklyReset(tools, data) {
    const copy = tools.weekly;
    const mode = data.reset || 'gentle';

    renderList('weeklyWinsList', lines(data.wins), copy.defaults.wins);
    renderList('weeklyLooseList', lines(data.loose), copy.defaults.loose);
    renderList('weeklyMustList', lines(data.must), copy.defaults.must);
    document.getElementById('weeklySupportText').textContent = data.support || copy.defaults.support;
    document.getElementById('weeklyResetList').innerHTML = (copy.sequences[mode] || copy.sequences.gentle)
        .map((step) => `<li>${step}</li>`)
        .join('');
}

function weeklyToText(tools, data) {
    const copy = tools.weekly;
    const mode = data.reset || 'gentle';

    return [
        copy.output_title,
        '',
        copy.sections.wins,
        ...withDefault(lines(data.wins), copy.defaults.wins).map((item) => `- ${item}`),
        '',
        copy.sections.loose,
        ...withDefault(lines(data.loose), copy.defaults.loose).map((item) => `- ${item}`),
        '',
        copy.sections.must,
        ...withDefault(lines(data.must), copy.defaults.must).map((item) => `- ${item}`),
        '',
        `${copy.sections.support}: ${data.support || copy.defaults.support}`,
        '',
        copy.sections.reset,
        ...(copy.sequences[mode] || copy.sequences.gentle).map((item) => `- ${item}`),
    ].join('\n');
}

function initCommunicationRepair(tools) {
    const form = document.getElementById('communicationForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.communication') ?? '{}');
    const fields = {
        situation: document.getElementById('communicationSituation'),
        person: document.getElementById('communicationPerson'),
        context: document.getElementById('communicationContext'),
        need: document.getElementById('communicationNeed'),
        tone: document.getElementById('communicationTone'),
    };

    hydrateFields(fields, saved);
    renderCommunicationRepair(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.communication', JSON.stringify(data));
        renderCommunicationRepair(tools, data);
    });

    document.getElementById('communicationPrintButton').addEventListener('click', () => window.print());
    document.getElementById('communicationDownloadButton').addEventListener('click', () => {
        downloadText('adhd-communication-repair.txt', communicationToText(tools, collectFields(fields)));
    });
    document.getElementById('communicationClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.communication');
        hydrateFields(fields, {});
        renderCommunicationRepair(tools, {});
    });
}

function renderCommunicationRepair(tools, data) {
    const copy = tools.communication;
    const situation = data.situation || 'late_reply';
    const person = data.person?.trim() || copy.defaults.person;

    document.getElementById('communicationOutputTitle').textContent = copy.output_title
        .replace(':situation', copy.situations[situation] || copy.situations.late_reply);
    document.getElementById('communicationMessageText').textContent = communicationMessage(copy, data, situation, person);
    renderList('communicationBeforeList', communicationBefore(copy, data), copy.before[0]);
    renderList('communicationRepairList', copy.repairs[situation] || copy.repairs.late_reply, copy.repairs.late_reply[0]);
    document.getElementById('communicationBoundaryText').textContent = data.need?.trim()
        ? copy.boundary_with_need.replace(':need', data.need.trim())
        : copy.boundary;
}

function communicationMessage(copy, data, situation, person) {
    const context = data.context?.trim() || copy.defaults.context[situation] || copy.defaults.context.late_reply;
    const need = data.need?.trim() || copy.defaults.need[situation] || copy.defaults.need.late_reply;
    const tone = data.tone || 'warm';
    const template = copy.messages[tone]?.[situation] || copy.messages.warm.late_reply;

    return template
        .replace(':person', person)
        .replace(':context', context)
        .replace(':need', need);
}

function communicationBefore(copy, data) {
    const before = [...copy.before];
    const context = data.context?.trim();

    if (context) {
        before.push(copy.context_note.replace(':context', context));
    }

    return before;
}

function communicationToText(tools, data) {
    const copy = tools.communication;
    const situation = data.situation || 'late_reply';
    const person = data.person?.trim() || copy.defaults.person;

    return [
        copy.output_title.replace(':situation', copy.situations[situation] || copy.situations.late_reply),
        '',
        copy.sections.message,
        communicationMessage(copy, data, situation, person),
        '',
        copy.sections.before,
        ...communicationBefore(copy, data).map((item) => `- ${item}`),
        '',
        copy.sections.repair,
        ...(copy.repairs[situation] || copy.repairs.late_reply).map((item) => `- ${item}`),
        '',
        copy.sections.boundary,
        data.need?.trim() ? copy.boundary_with_need.replace(':need', data.need.trim()) : copy.boundary,
    ].join('\n');
}

function initEnergyCrash(tools) {
    const form = document.getElementById('energyForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.energy') ?? '{}');
    const fields = {
        state: document.getElementById('energyState'),
        must: document.getElementById('energyMust'),
        body: document.getElementById('energyBody'),
        drop: document.getElementById('energyDrop'),
        support: document.getElementById('energySupport'),
        time: document.getElementById('energyTime'),
    };

    hydrateFields(fields, saved);
    renderEnergyCrash(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.energy', JSON.stringify(data));
        renderEnergyCrash(tools, data);
    });

    document.getElementById('energyPrintButton').addEventListener('click', () => window.print());
    document.getElementById('energyDownloadButton').addEventListener('click', () => {
        downloadText('adhd-energy-crash-plan.txt', energyToText(tools, collectFields(fields)));
    });
    document.getElementById('energyClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.energy');
        hydrateFields(fields, {});
        renderEnergyCrash(tools, {});
    });
}

function renderEnergyCrash(tools, data) {
    const plan = energyPlan(tools.energy, data);

    document.getElementById('energyOutputTitle').textContent = plan.title;
    document.getElementById('energyFirstText').textContent = plan.first;
    document.getElementById('energyBodyText').textContent = plan.body;
    document.getElementById('energyStepList').innerHTML = plan.steps
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('energyDropText').textContent = plan.drop;
    document.getElementById('energyScriptText').textContent = plan.script;
    document.getElementById('energyStopText').textContent = plan.stop;
}

function energyPlan(copy, data) {
    const state = data.state || 'empty';
    const body = data.body || 'water';
    const time = data.time || '10';
    const must = data.must?.trim() || copy.defaults.must;
    const drop = data.drop?.trim() || copy.defaults.drop;
    const support = data.support?.trim() || copy.defaults.support;

    return {
        title: copy.output_title.replace(':state', copy.states[state] || copy.states.empty),
        first: copy.first_action
            .replace(':time', copy.times[time] || copy.times['10'])
            .replace(':must', must),
        body: copy.body_resets[body] || copy.body_resets.water,
        steps: copy.steps[state] || copy.steps.empty,
        drop: copy.drop_rule.replace(':drop', drop),
        script: copy.support_script
            .replace(':support', support)
            .replace(':must', must)
            .replace(':time', copy.times[time] || copy.times['10']),
        stop: copy.stop_rule,
    };
}

function energyToText(tools, data) {
    const copy = tools.energy;
    const plan = energyPlan(copy, data);

    return [
        plan.title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.body,
        plan.body,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.drop,
        plan.drop,
        '',
        copy.sections.script,
        plan.script,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initDecisionPriority(tools) {
    const form = document.getElementById('decisionForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.decision') ?? '{}');
    const fields = {
        options: document.getElementById('decisionOptions'),
        urgency: document.getElementById('decisionUrgency'),
        energy: document.getElementById('decisionEnergy'),
        consequence: document.getElementById('decisionConsequence'),
        relief: document.getElementById('decisionRelief'),
        support: document.getElementById('decisionSupport'),
    };

    hydrateFields(fields, saved);
    renderDecisionPriority(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.decision', JSON.stringify(data));
        renderDecisionPriority(tools, data);
    });

    document.getElementById('decisionPrintButton').addEventListener('click', () => window.print());
    document.getElementById('decisionDownloadButton').addEventListener('click', () => {
        downloadText('adhd-decision-priority-card.txt', decisionToText(tools, collectFields(fields)));
    });
    document.getElementById('decisionClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.decision');
        hydrateFields(fields, {});
        renderDecisionPriority(tools, {});
    });
}

function renderDecisionPriority(tools, data) {
    const plan = decisionPlan(tools.decision, data);

    document.getElementById('decisionOutputTitle').textContent = plan.title;
    document.getElementById('decisionChosenText').textContent = plan.chosen;
    document.getElementById('decisionWhyText').textContent = plan.why;
    document.getElementById('decisionFirstText').textContent = plan.first;
    document.getElementById('decisionStepList').innerHTML = plan.steps
        .map((step) => `<li>${step}</li>`)
        .join('');
    renderList('decisionParkedList', plan.parked, tools.decision.defaults.parked);
    document.getElementById('decisionScriptText').textContent = plan.script;
    document.getElementById('decisionStopText').textContent = plan.stop;
}

function decisionPlan(copy, data) {
    const options = lines(data.options);
    const relief = data.relief?.trim();
    const chosen = relief || options[0] || copy.defaults.chosen;
    const parked = options.filter((option) => option !== chosen);
    const energy = data.energy || 'low';
    const consequence = data.consequence || 'unsure';
    const support = data.support?.trim() || copy.defaults.support;

    return {
        title: copy.output_title.replace(':choice', chosen),
        chosen,
        parked,
        why: copy.reasons[consequence] || copy.reasons.unsure,
        first: copy.first_action.replace(':choice', chosen),
        steps: copy.steps[energy] || copy.steps.low,
        script: copy.support_script
            .replace(':support', support)
            .replace(':choice', chosen),
        stop: copy.stop_rule,
    };
}

function decisionToText(tools, data) {
    const copy = tools.decision;
    const plan = decisionPlan(copy, data);

    return [
        plan.title,
        '',
        `${copy.sections.chosen}: ${plan.chosen}`,
        '',
        `${copy.sections.why}: ${plan.why}`,
        '',
        `${copy.sections.first}: ${plan.first}`,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.parked,
        ...withDefault(plan.parked, copy.defaults.parked).map((item) => `- ${item}`),
        '',
        copy.sections.script,
        plan.script,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initFocusSprint(tools) {
    const form = document.getElementById('focusForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.focus') ?? '{}');
    const fields = {
        task: document.getElementById('focusTask'),
        mode: document.getElementById('focusMode'),
        minutes: document.getElementById('focusMinutes'),
        distraction: document.getElementById('focusDistraction'),
        support: document.getElementById('focusSupport'),
        reward: document.getElementById('focusReward'),
    };

    hydrateFields(fields, saved);
    renderFocusSprint(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.focus', JSON.stringify(data));
        renderFocusSprint(tools, data);
    });

    document.getElementById('focusPrintButton').addEventListener('click', () => window.print());
    document.getElementById('focusDownloadButton').addEventListener('click', () => {
        downloadText('adhd-focus-sprint-card.txt', focusToText(tools, collectFields(fields)));
    });
    document.getElementById('focusClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.focus');
        hydrateFields(fields, {});
        renderFocusSprint(tools, {});
    });
}

function renderFocusSprint(tools, data) {
    const plan = focusPlan(tools.focus, data);

    document.getElementById('focusOutputTitle').textContent = plan.title;
    document.getElementById('focusStartText').textContent = plan.start;
    document.getElementById('focusStepList').innerHTML = plan.steps
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('focusDistractionText').textContent = plan.distraction;
    document.getElementById('focusSupportText').textContent = plan.support;
    document.getElementById('focusRewardText').textContent = plan.reward;
    document.getElementById('focusStopText').textContent = plan.stop;
}

function focusPlan(copy, data) {
    const task = data.task?.trim() || copy.defaults.task;
    const mode = data.mode || 'start';
    const minutes = data.minutes || '10';
    const distraction = data.distraction?.trim() || copy.defaults.distraction;
    const support = copy.supports[data.support || 'timer'] || copy.supports.timer;
    const reward = data.reward?.trim() || copy.defaults.reward;

    return {
        title: copy.output_title.replace(':task', task),
        start: copy.start_cue
            .replace(':minutes', copy.minutes[minutes] || copy.minutes['10'])
            .replace(':task', task),
        steps: copy.steps[mode] || copy.steps.start,
        distraction: copy.distraction_rule.replace(':distraction', distraction),
        support: copy.support_prompt.replace(':support', support),
        reward: copy.reward_rule.replace(':reward', reward),
        stop: copy.stop_rule,
    };
}

function focusToText(tools, data) {
    const copy = tools.focus;
    const plan = focusPlan(copy, data);

    return [
        plan.title,
        '',
        copy.sections.start,
        plan.start,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.distraction,
        plan.distraction,
        '',
        copy.sections.support,
        plan.support,
        '',
        copy.sections.reward,
        plan.reward,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initEmotionalReset(tools) {
    const form = document.getElementById('emotionForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.emotion') ?? '{}');
    const fields = {
        trigger: document.getElementById('emotionTrigger'),
        intensity: document.getElementById('emotionIntensity'),
        body: document.getElementById('emotionBody'),
        story: document.getElementById('emotionStory'),
        next: document.getElementById('emotionNext'),
        support: document.getElementById('emotionSupport'),
    };

    hydrateFields(fields, saved);
    renderEmotionalReset(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.emotion', JSON.stringify(data));
        renderEmotionalReset(tools, data);
    });

    document.getElementById('emotionPrintButton').addEventListener('click', () => window.print());
    document.getElementById('emotionDownloadButton').addEventListener('click', () => {
        downloadText('adhd-emotional-reset-card.txt', emotionToText(tools, collectFields(fields)));
    });
    document.getElementById('emotionClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.emotion');
        hydrateFields(fields, {});
        renderEmotionalReset(tools, {});
    });
}

function renderEmotionalReset(tools, data) {
    const plan = emotionPlan(tools.emotion, data);

    document.getElementById('emotionOutputTitle').textContent = plan.title;
    document.getElementById('emotionFirstText').textContent = plan.first;
    document.getElementById('emotionGroundList').innerHTML = plan.ground
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('emotionReframeText').textContent = plan.reframe;
    document.getElementById('emotionNextText').textContent = plan.next;
    document.getElementById('emotionScriptText').textContent = plan.script;
    document.getElementById('emotionStopText').textContent = plan.stop;
}

function emotionPlan(copy, data) {
    const trigger = data.trigger?.trim() || copy.defaults.trigger;
    const intensity = data.intensity || 'medium';
    const body = data.body || 'unknown';
    const story = data.story?.trim() || copy.defaults.story;
    const next = data.next?.trim() || copy.defaults.next;
    const support = data.support?.trim() || copy.defaults.support;

    return {
        title: copy.output_title.replace(':trigger', trigger),
        first: copy.body_resets[body] || copy.body_resets.unknown,
        ground: copy.grounding[intensity] || copy.grounding.medium,
        reframe: copy.reframe
            .replace(':story', story),
        next: copy.next_action.replace(':next', next),
        script: copy.support_script
            .replace(':support', support)
            .replace(':trigger', trigger),
        stop: copy.stop_rule,
    };
}

function emotionToText(tools, data) {
    const copy = tools.emotion;
    const plan = emotionPlan(copy, data);

    return [
        plan.title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.ground,
        ...plan.ground.map((item) => `- ${item}`),
        '',
        copy.sections.reframe,
        plan.reframe,
        '',
        copy.sections.next,
        plan.next,
        '',
        copy.sections.script,
        plan.script,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initTransitionRescue(tools) {
    const form = document.getElementById('transitionForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.transition') ?? '{}');
    const fields = {
        transition: document.getElementById('transitionName'),
        mode: document.getElementById('transitionMode'),
        time: document.getElementById('transitionTime'),
        anchor: document.getElementById('transitionAnchor'),
        blocker: document.getElementById('transitionBlocker'),
        support: document.getElementById('transitionSupport'),
    };

    hydrateFields(fields, saved);
    renderTransitionRescue(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.transition', JSON.stringify(data));
        renderTransitionRescue(tools, data);
    });

    document.getElementById('transitionPrintButton').addEventListener('click', () => window.print());
    document.getElementById('transitionDownloadButton').addEventListener('click', () => {
        downloadText('adhd-transition-rescue-card.txt', transitionToText(tools, collectFields(fields)));
    });
    document.getElementById('transitionClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.transition');
        hydrateFields(fields, {});
        renderTransitionRescue(tools, {});
    });
}

function renderTransitionRescue(tools, data) {
    const plan = transitionPlan(tools.transition, data);

    document.getElementById('transitionOutputTitle').textContent = plan.title;
    document.getElementById('transitionFirstText').textContent = plan.first;
    document.getElementById('transitionStepsList').innerHTML = plan.steps
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('transitionParkingList').innerHTML = plan.parking
        .map((step) => `<li>${step}</li>`)
        .join('');
    document.getElementById('transitionAnchorText').textContent = plan.anchorCue;
    document.getElementById('transitionScriptText').textContent = plan.script;
    document.getElementById('transitionStopText').textContent = plan.stop;
}

function transitionPlan(copy, data) {
    const transition = data.transition?.trim() || copy.defaults.transition;
    const mode = data.mode || 'unsure';
    const time = data.time?.trim() || copy.defaults.time;
    const anchor = data.anchor?.trim() || copy.defaults.anchor;
    const blocker = data.blocker?.trim() || copy.defaults.blocker;
    const support = data.support?.trim() || copy.defaults.support;

    return {
        title: copy.output_title.replace(':transition', transition),
        first: copy.first_actions[mode] || copy.first_actions.unsure,
        steps: copy.mode_steps[mode] || copy.mode_steps.unsure,
        parking: [
            copy.parking.blocker.replace(':blocker', blocker),
            copy.parking.anchor.replace(':anchor', anchor),
            copy.parking.time.replace(':time', time),
        ],
        anchorCue: copy.anchor_cue
            .replace(':anchor', anchor)
            .replace(':transition', transition),
        script: copy.support_script
            .replace(':support', support)
            .replace(':transition', transition)
            .replace(':time', time),
        stop: copy.stop_rule,
    };
}

function transitionToText(tools, data) {
    const copy = tools.transition;
    const plan = transitionPlan(copy, data);

    return [
        plan.title,
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.steps,
        ...plan.steps.map((item) => `- ${item}`),
        '',
        copy.sections.parking,
        ...plan.parking.map((item) => `- ${item}`),
        '',
        copy.sections.anchor,
        plan.anchorCue,
        '',
        copy.sections.script,
        plan.script,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function initAppointmentPrep(tools) {
    const form = document.getElementById('appointmentForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.appointment') ?? '{}');
    const fields = {
        provider: document.getElementById('appointmentProvider'),
        symptoms: document.getElementById('appointmentSymptoms'),
        impact: document.getElementById('appointmentImpact'),
        questions: document.getElementById('appointmentQuestions'),
        contact: document.getElementById('appointmentContact'),
    };

    hydrateFields(fields, saved);
    renderAppointmentPrep(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.appointment', JSON.stringify(data));
        renderAppointmentPrep(tools, data);
    });

    document.getElementById('appointmentPrintButton').addEventListener('click', () => window.print());
    document.getElementById('appointmentDownloadButton').addEventListener('click', () => {
        downloadText('adhd-appointment-prep.txt', appointmentToText(tools, collectFields(fields)));
    });
    document.getElementById('appointmentClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.appointment');
        hydrateFields(fields, {});
        renderAppointmentPrep(tools, {});
    });
}

function renderAppointmentPrep(tools, data) {
    const copy = tools.appointment;
    const provider = data.provider?.trim() || copy.defaults.provider;
    const symptoms = data.symptoms?.trim() || copy.defaults.symptoms;
    const impact = data.impact?.trim() || copy.defaults.impact;
    const contact = data.contact || 'call';

    document.getElementById('appointmentOutputTitle').textContent = `${copy.output_title}: ${provider}`;
    renderList('appointmentBringList', copy.defaults.bring, copy.defaults.bring[0]);
    document.getElementById('appointmentScript').textContent = buildAppointmentScript(copy, contact, symptoms, impact);
    renderList('appointmentQuestionList', withDefault(lines(data.questions), copy.defaults.questions[0]), copy.defaults.questions[0]);
    document.getElementById('appointmentNotes').textContent = copy.defaults.note;
}

function buildAppointmentScript(copy, contact, symptoms, impact) {
    const template = copy.scripts[contact] || copy.scripts.call;

    return `${template} ${copy.fields.symptoms}: ${symptoms}. ${copy.fields.impact}: ${impact}.`;
}

function appointmentToText(tools, data) {
    const copy = tools.appointment;
    const provider = data.provider?.trim() || copy.defaults.provider;

    return [
        `${copy.output_title}: ${provider}`,
        '',
        copy.sections.bring,
        ...copy.defaults.bring.map((item) => `- ${item}`),
        '',
        copy.sections.say,
        buildAppointmentScript(copy, data.contact || 'call', data.symptoms || copy.defaults.symptoms, data.impact || copy.defaults.impact),
        '',
        copy.sections.questions,
        ...withDefault(lines(data.questions), copy.defaults.questions[0]).map((item) => `- ${item}`),
        '',
        copy.sections.notes,
        copy.defaults.note,
    ].join('\n');
}

function initCareNotes(tools) {
    const form = document.getElementById('careForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.care') ?? '{}');
    const fields = {
        main: document.getElementById('careMain'),
        history: document.getElementById('careHistory'),
        settings: document.getElementById('careSettings'),
        overlap: document.getElementById('careOverlap'),
        help: document.getElementById('careHelp'),
    };

    hydrateFields(fields, saved);
    renderCareNotes(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.care', JSON.stringify(data));
        renderCareNotes(tools, data);
    });

    document.getElementById('carePrintButton').addEventListener('click', () => window.print());
    document.getElementById('careDownloadButton').addEventListener('click', () => {
        downloadText('adhd-care-discussion-notes.txt', careToText(tools, collectFields(fields)));
    });
    document.getElementById('careClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.care');
        hydrateFields(fields, {});
        renderCareNotes(tools, {});
    });
}

function renderCareNotes(tools, data) {
    const copy = tools.care;
    const main = data.main?.trim();

    document.getElementById('careOutputTitle').textContent = main || copy.empty_title;
    document.getElementById('careOpening').textContent = buildCareOpening(copy, data);
    renderList('careHistoryList', withDefault(lines(data.history), copy.defaults.history), copy.defaults.history[0]);
    renderList('careSettingsList', withDefault(lines(data.settings), copy.defaults.settings), copy.defaults.settings[0]);
    renderList('careOverlapList', withDefault(lines(data.overlap), copy.defaults.overlap), copy.defaults.overlap[0]);
    renderList('careAskList', careAsks(copy, data), copy.defaults.ask[0]);
}

function buildCareOpening(copy, data) {
    const main = data.main?.trim() || copy.defaults.main;
    const help = data.help?.trim() || copy.defaults.help;

    return copy.opening
        .replace(':main', main)
        .replace(':help', help);
}

function careAsks(copy, data) {
    const asks = [...copy.defaults.ask];
    const help = data.help?.trim();

    if (help) {
        asks.unshift(`${copy.labels.help}: ${help}`);
    }

    return asks;
}

function careToText(tools, data) {
    const copy = tools.care;

    return [
        data.main?.trim() || copy.empty_title,
        '',
        copy.sections.opening,
        buildCareOpening(copy, data),
        '',
        copy.sections.history,
        ...withDefault(lines(data.history), copy.defaults.history).map((item) => `- ${item}`),
        '',
        copy.sections.settings,
        ...withDefault(lines(data.settings), copy.defaults.settings).map((item) => `- ${item}`),
        '',
        copy.sections.overlap,
        ...withDefault(lines(data.overlap), copy.defaults.overlap).map((item) => `- ${item}`),
        '',
        copy.sections.ask,
        ...careAsks(copy, data).map((item) => `- ${item}`),
    ].join('\n');
}

function initSupportRequest(tools) {
    const form = document.getElementById('supportForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.support') ?? '{}');
    const fields = {
        audience: document.getElementById('supportAudience'),
        situation: document.getElementById('supportSituation'),
        barrier: document.getElementById('supportBarrier'),
        request: document.getElementById('supportRequest'),
        trial: document.getElementById('supportTrial'),
        tone: document.getElementById('supportTone'),
    };

    hydrateFields(fields, saved);
    renderSupportRequest(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.support', JSON.stringify(data));
        renderSupportRequest(tools, data);
    });

    document.getElementById('supportPrintButton').addEventListener('click', () => window.print());
    document.getElementById('supportDownloadButton').addEventListener('click', () => {
        downloadText('adhd-support-request.txt', supportToText(tools, collectFields(fields)));
    });
    document.getElementById('supportClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.support');
        hydrateFields(fields, {});
        renderSupportRequest(tools, {});
    });
}

function renderSupportRequest(tools, data) {
    const copy = tools.support;
    const audience = data.audience || 'work';
    const request = data.request?.trim() || copy.defaults.request[audience] || copy.defaults.request.work;

    document.getElementById('supportOutputTitle').textContent = data.situation?.trim() || copy.empty_title;
    document.getElementById('supportMessage').textContent = buildSupportMessage(copy, data);
    renderList('supportAskList', supportAskList(copy, data, request), request);
    document.getElementById('supportExperiment').textContent = data.trial?.trim() || copy.defaults.trial;
    document.getElementById('supportFollowUp').textContent = copy.follow_up
        .replace(':time', copy.review_times[audience] || copy.review_times.work);
}

function buildSupportMessage(copy, data) {
    const audience = data.audience || 'work';
    const tone = data.tone || 'warm';
    const situation = data.situation?.trim() || copy.defaults.situation[audience] || copy.defaults.situation.work;
    const barrier = data.barrier?.trim() || copy.defaults.barrier;
    const request = data.request?.trim() || copy.defaults.request[audience] || copy.defaults.request.work;
    const template = copy.messages[tone] || copy.messages.warm;

    return template
        .replace(':situation', situation)
        .replace(':barrier', barrier)
        .replace(':request', request);
}

function supportAskList(copy, data, request) {
    const asks = [request, ...copy.defaults.asks];
    const barrier = data.barrier?.trim();

    if (barrier) {
        asks.push(`${copy.labels.barrier}: ${barrier}`);
    }

    return asks;
}

function supportToText(tools, data) {
    const copy = tools.support;
    const audience = data.audience || 'work';
    const request = data.request?.trim() || copy.defaults.request[audience] || copy.defaults.request.work;

    return [
        data.situation?.trim() || copy.empty_title,
        '',
        copy.sections.message,
        buildSupportMessage(copy, data),
        '',
        copy.sections.ask,
        ...supportAskList(copy, data, request).map((item) => `- ${item}`),
        '',
        copy.sections.experiment,
        data.trial?.trim() || copy.defaults.trial,
        '',
        copy.sections.follow_up,
        copy.follow_up.replace(':time', copy.review_times[audience] || copy.review_times.work),
    ].join('\n');
}

function initWorkSchoolSupport(tools) {
    const form = document.getElementById('workSchoolForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.workschool') ?? '{}');
    const fields = {
        setting: document.getElementById('workSchoolSetting'),
        challenge: document.getElementById('workSchoolChallenge'),
        supportStyle: document.getElementById('workSchoolSupportStyle'),
        friction: document.getElementById('workSchoolFriction'),
        person: document.getElementById('workSchoolPerson'),
        trial: document.getElementById('workSchoolTrial'),
    };

    hydrateFields(fields, {
        setting: 'work',
        challenge: 'focus',
        supportStyle: 'written',
        ...saved,
    });
    renderWorkSchoolSupport(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.workschool', JSON.stringify(data));
        renderWorkSchoolSupport(tools, data);
    });

    document.getElementById('workSchoolPrintButton').addEventListener('click', () => window.print());
    document.getElementById('workSchoolDownloadButton').addEventListener('click', () => {
        downloadText('adhd-work-school-support.txt', workSchoolSupportToText(tools, collectFields(fields)));
    });
    document.getElementById('workSchoolClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.workschool');
        hydrateFields(fields, { setting: 'work', challenge: 'focus', supportStyle: 'written' });
        renderWorkSchoolSupport(tools, collectFields(fields));
    });
}

function renderWorkSchoolSupport(tools, data) {
    const plan = workSchoolSupportPlan(tools.workschool, data);

    document.getElementById('workSchoolOutputTitle').textContent = tools.workschool.output_title.replace(':setting', plan.settingLabel);
    document.getElementById('workSchoolFirstText').textContent = plan.first;
    renderList('workSchoolOptionList', plan.options, tools.workschool.defaults.option);
    document.getElementById('workSchoolScriptText').textContent = plan.script;
    document.getElementById('workSchoolTrialText').textContent = plan.trial;
    renderList('workSchoolReviewList', plan.review, tools.workschool.defaults.review);
    document.getElementById('workSchoolBoundaryText').textContent = plan.boundary;
}

function workSchoolSupportPlan(copy, data) {
    const setting = copy.settings[data.setting] ? data.setting : 'work';
    const challenge = copy.challenge_options[data.challenge] ? data.challenge : 'focus';
    const supportStyle = copy.style_options[data.supportStyle] ? data.supportStyle : 'written';
    const friction = data.friction?.trim() || copy.defaults.friction;
    const person = data.person?.trim() || copy.defaults.person[setting] || copy.defaults.person.work;
    const trial = data.trial?.trim() || copy.defaults.trial;

    return {
        settingLabel: copy.settings[setting],
        first: copy.first_ask
            .replace(':setting', copy.setting_words[setting] || copy.setting_words.work)
            .replace(':trial', trial),
        options: [
            copy.style_options[supportStyle],
            ...copy.challenge_options[challenge],
        ],
        script: copy.script
            .replace(':person', person)
            .replace(':setting', copy.setting_words[setting] || copy.setting_words.work)
            .replace(':friction', friction)
            .replace(':trial', trial),
        trial: copy.trial_plan.replace(':trial', trial),
        review: copy.review_points,
        boundary: copy.boundary,
    };
}

function workSchoolSupportToText(tools, data) {
    const copy = tools.workschool;
    const plan = workSchoolSupportPlan(copy, data);

    return [
        copy.output_title.replace(':setting', plan.settingLabel),
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.options,
        ...plan.options.map((item) => `- ${item}`),
        '',
        copy.sections.script,
        plan.script,
        '',
        copy.sections.trial,
        plan.trial,
        '',
        copy.sections.review,
        ...plan.review.map((item) => `- ${item}`),
        '',
        copy.sections.boundary,
        plan.boundary,
    ].join('\n');
}

function initProviderShortlist(tools) {
    const form = document.getElementById('providerForm');
    const fields = {
        name: document.getElementById('providerName'),
        kind: document.getElementById('providerKind'),
        contact: document.getElementById('providerContact'),
        cost: document.getElementById('providerCost'),
        status: document.getElementById('providerStatus'),
        notes: document.getElementById('providerNotes'),
    };

    renderProviders(tools, readProviders());

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const providers = readProviders();
        providers.push({ ...collectFields(fields), id: Date.now() });
        supportStorage.setItem('adhdSupport.providers', JSON.stringify(providers));
        hydrateFields(fields, {});
        renderProviders(tools, providers);
    });

    document.getElementById('providersDownloadButton').addEventListener('click', () => {
        downloadText('adhd-provider-shortlist.txt', providersToText(tools, readProviders()));
    });

    document.getElementById('providersClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.providers');
        renderProviders(tools, []);
    });
}

function readProviders() {
    const providers = readJson('adhdSupport.providers');

    return Array.isArray(providers) ? providers : [];
}

function renderProviders(tools, providers) {
    const copy = tools.providers;
    const list = document.getElementById('providersList');

    if (providers.length === 0) {
        list.innerHTML = `<article class="provider-card"><p>${copy.empty}</p></article>`;

        return;
    }

    list.innerHTML = providers.map((provider) => `
        <article class="provider-card">
            <h3>${provider.name}</h3>
            <dl>
                ${provider.kind ? `<div><dt>${copy.labels.kind}</dt><dd>${provider.kind}</dd></div>` : ''}
                ${provider.contact ? `<div><dt>${copy.labels.contact}</dt><dd>${provider.contact}</dd></div>` : ''}
                ${provider.cost ? `<div><dt>${copy.labels.cost}</dt><dd>${provider.cost}</dd></div>` : ''}
                <div><dt>${copy.labels.status}</dt><dd>${copy.statuses[provider.status] || copy.statuses.saved}</dd></div>
                ${provider.notes ? `<div><dt>${copy.labels.notes}</dt><dd>${provider.notes}</dd></div>` : ''}
            </dl>
        </article>
    `).join('');
}

function providersToText(tools, providers) {
    const copy = tools.providers;

    if (providers.length === 0) {
        return copy.empty;
    }

    return providers.flatMap((provider, index) => [
        `${index + 1}. ${provider.name}`,
        provider.kind ? `${copy.labels.kind}: ${provider.kind}` : '',
        provider.contact ? `${copy.labels.contact}: ${provider.contact}` : '',
        provider.cost ? `${copy.labels.cost}: ${provider.cost}` : '',
        `${copy.labels.status}: ${copy.statuses[provider.status] || copy.statuses.saved}`,
        provider.notes ? `${copy.labels.notes}: ${provider.notes}` : '',
        '',
    ].filter(Boolean)).join('\n');
}

function initAccessPlan(tools) {
    const form = document.getElementById('accessForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.access') ?? '{}');
    const fields = {
        barrier: document.getElementById('accessBarrier'),
        location: document.getElementById('accessLocation'),
        budget: document.getElementById('accessBudget'),
        support: document.getElementById('accessSupport'),
        notes: document.getElementById('accessNotes'),
    };

    hydrateFields(fields, saved);
    renderAccessPlan(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.access', JSON.stringify(data));
        renderAccessPlan(tools, data);
    });

    document.getElementById('accessDownloadButton').addEventListener('click', () => {
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.access', JSON.stringify(data));
        renderAccessPlan(tools, data);
        downloadText('adhd-access-plan.txt', accessToText(tools, data));
    });

    document.getElementById('accessClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.access');
        hydrateFields(fields, {});
        renderAccessPlan(tools, {});
    });
}

function renderAccessPlan(tools, data) {
    const copy = tools.access;
    const barrier = data.barrier || 'unsure';
    const barrierLabel = copy.barriers[barrier] || copy.barriers.unsure;

    document.getElementById('accessOutputTitle').textContent = `${copy.output_title}: ${barrierLabel}`;
    document.getElementById('accessFirstText').textContent = copy.first_steps[barrier] || copy.first_steps.unsure;
    renderList('accessAskList', accessQuestions(copy, data), copy.questions[0]);
    renderList('accessFallbackList', copy.fallbacks, copy.fallbacks[0]);
}

function accessQuestions(copy, data) {
    return [
        ...copy.questions,
        data.budget ? `${copy.fields.budget}: ${data.budget}` : '',
        data.location ? `${copy.fields.location}: ${data.location}` : '',
        data.support ? `${copy.fields.support}: ${data.support}` : '',
        data.notes ? `${copy.fields.notes}: ${data.notes}` : '',
    ].filter(Boolean);
}

function accessToText(tools, data) {
    const copy = tools.access;
    const barrier = data.barrier || 'unsure';

    return [
        copy.output_title,
        `${copy.fields.barrier}: ${copy.barriers[barrier] || copy.barriers.unsure}`,
        '',
        copy.sections.first,
        copy.first_steps[barrier] || copy.first_steps.unsure,
        '',
        copy.sections.ask,
        ...accessQuestions(copy, data).map((item) => `- ${item}`),
        '',
        copy.sections.fallback,
        ...copy.fallbacks.map((item) => `- ${item}`),
        '',
        copy.sections.sources,
        ...copy.sources.map((source) => `- ${source.label}: ${source.url}`),
    ].join('\n');
}

function initTaskBreakdown(tools) {
    const form = document.getElementById('taskForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.task') ?? '{}');
    const fields = {
        dump: document.getElementById('taskDump'),
        focus: document.getElementById('taskFocus'),
        stuck: document.getElementById('taskStuck'),
        done: document.getElementById('taskDone'),
    };

    hydrateFields(fields, saved);
    renderTaskBreakdown(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.task', JSON.stringify(data));
        renderTaskBreakdown(tools, data);
    });

    document.getElementById('taskPrintButton').addEventListener('click', () => window.print());
    document.getElementById('taskDownloadButton').addEventListener('click', () => {
        downloadText('adhd-task-breakdown.txt', taskToText(tools, collectFields(fields)));
    });
    document.getElementById('taskClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.task');
        hydrateFields(fields, {});
        renderTaskBreakdown(tools, {});
    });
}

function renderTaskBreakdown(tools, data) {
    const copy = tools.task;
    const task = data.focus?.trim();
    const stuck = data.stuck || 'unclear';
    const steps = taskMicrosteps(copy, task, stuck);
    const nowItems = task ? copy.defaults.now : [copy.empty_copy];
    const hidden = taskHiddenSteps(copy, data.dump);

    document.getElementById('taskOutputTitle').textContent = task || copy.empty_title;
    renderList('taskNowList', nowItems, copy.empty_copy);
    document.getElementById('taskMicrosteps').innerHTML = task
        ? steps.map((step) => `<li>${step}</li>`).join('')
        : '';
    renderList('taskHiddenList', hidden, copy.defaults.hidden[0]);
    document.getElementById('taskDoneText').textContent = data.done?.trim() || copy.defaults.done;
    document.getElementById('taskResetText').textContent = copy.defaults.reset;
}

function taskHiddenSteps(copy, dump) {
    const dumpedItems = lines(dump).slice(0, 4);

    if (dumpedItems.length === 0) {
        return copy.defaults.hidden;
    }

    return [
        ...copy.defaults.hidden,
        ...dumpedItems.map((item) => `${copy.labels.park}: ${item}`),
    ];
}

function taskMicrosteps(copy, task, stuck) {
    if (! task) {
        return [];
    }

    return [
        `${copy.fields.focus}: ${task}.`,
        ...(copy.templates[stuck] || copy.templates.unclear),
    ];
}

function taskToText(tools, data) {
    const copy = tools.task;
    const task = data.focus?.trim() || copy.empty_title;
    const hidden = taskHiddenSteps(copy, data.dump);

    return [
        task,
        '',
        copy.sections.now,
        ...copy.defaults.now.map((item) => `- ${item}`),
        '',
        copy.sections.microsteps,
        ...taskMicrosteps(copy, data.focus, data.stuck || 'unclear').map((item) => `- ${item}`),
        '',
        copy.sections.hidden,
        ...hidden.map((item) => `- ${item}`),
        '',
        copy.sections.done,
        data.done?.trim() || copy.defaults.done,
        '',
        copy.sections.reset,
        copy.defaults.reset,
    ].join('\n');
}

function initDashboard(tools, locale) {
    const copy = tools.dashboard;
    const saved = readSupportKit(locale);

    renderDashboard(copy, saved);

    document.getElementById('dashboardExportButton').addEventListener('click', () => {
        downloadText('adhd-support-kit.json', JSON.stringify(readSupportKit(locale), null, 2));
    });

    document.getElementById('dashboardClearButton').addEventListener('click', () => {
        [
            'adhdSupport.latestResult',
            `adhdSupport.latestResult.${locale}`,
            'adhdSupport.navigator',
            'adhdSupport.goal',
            'adhdSupport.planner',
            'adhdSupport.time',
            'adhdSupport.body',
            'adhdSupport.meds',
            'adhdSupport.food',
            'adhdSupport.sleep',
            'adhdSupport.motivation',
            'adhdSupport.accountability',
            'adhdSupport.routine',
            'adhdSupport.weekly',
            'adhdSupport.communication',
            'adhdSupport.energy',
            'adhdSupport.decision',
            'adhdSupport.focus',
            'adhdSupport.emotion',
            'adhdSupport.transition',
            'adhdSupport.appointment',
            'adhdSupport.care',
            'adhdSupport.support',
            'adhdSupport.workschool',
            'adhdSupport.home',
            'adhdSupport.laundry',
            'adhdSupport.digital',
            'adhdSupport.money',
            'adhdSupport.lost',
            'adhdSupport.errand',
            'adhdSupport.providers',
            'adhdSupport.access',
            'adhdSupport.task',
            'adhdSupport.reminders',
            'adhdSupport.tracker',
        ].forEach((key) => supportStorage.removeItem(key));

        renderDashboard(copy, readSupportKit(locale));
        document.getElementById('dashboardNextBody').textContent = copy.cleared;
    });
}

function readSupportKit(locale = 'en') {
    return {
        result: localizedSavedResult(locale),
        navigator: readJson('adhdSupport.navigator'),
        goal: readJson('adhdSupport.goal'),
        planner: readJson('adhdSupport.planner'),
        time: readJson('adhdSupport.time'),
        body: readJson('adhdSupport.body'),
        meds: readJson('adhdSupport.meds'),
        food: readJson('adhdSupport.food'),
        sleep: readJson('adhdSupport.sleep'),
        motivation: readJson('adhdSupport.motivation'),
        accountability: readJson('adhdSupport.accountability'),
        routine: readJson('adhdSupport.routine'),
        weekly: readJson('adhdSupport.weekly'),
        communication: readJson('adhdSupport.communication'),
        energy: readJson('adhdSupport.energy'),
        decision: readJson('adhdSupport.decision'),
        focus: readJson('adhdSupport.focus'),
        emotion: readJson('adhdSupport.emotion'),
        transition: readJson('adhdSupport.transition'),
        appointment: readJson('adhdSupport.appointment'),
        care: readJson('adhdSupport.care'),
        support: readJson('adhdSupport.support'),
        workschool: readJson('adhdSupport.workschool'),
        home: readJson('adhdSupport.home'),
        laundry: readJson('adhdSupport.laundry'),
        digital: readJson('adhdSupport.digital'),
        money: readJson('adhdSupport.money'),
        lost: readJson('adhdSupport.lost'),
        errand: readJson('adhdSupport.errand'),
        providers: readProviders(),
        access: readJson('adhdSupport.access'),
        task: readJson('adhdSupport.task'),
        reminders: readJson('adhdSupport.reminders'),
        tracker: readJson('adhdSupport.tracker'),
    };
}

function localizedSavedResult(locale) {
    const localized = readJson(`adhdSupport.latestResult.${locale}`);

    if (localized) {
        return localized;
    }

    const legacy = readJson('adhdSupport.latestResult');

    return legacy?.locale === locale ? legacy : null;
}

function readJson(key) {
    try {
        return JSON.parse(supportStorage.getItem(key) || 'null');
    } catch {
        return null;
    }
}

function renderDashboard(copy, saved) {
    const summaries = {
        result: saved.result?.title,
        navigator: saved.navigator?.topic || saved.navigator?.knownNext,
        goal: saved.goal?.goal,
        planner: firstLine(saved.planner?.must),
        time: firstLine(saved.time?.must) || saved.time?.start,
        body: saved.body?.task || saved.body?.food,
        meds: saved.meds?.medicine || saved.meds?.mode,
        food: saved.food?.available || saved.food?.energy,
        sleep: saved.sleep?.wakeTime || saved.sleep?.mode,
        motivation: saved.motivation?.task || saved.motivation?.reward,
        accountability: saved.accountability?.task || saved.accountability?.person,
        routine: saved.routine?.kind || firstLine(saved.routine?.must),
        weekly: firstLine(saved.weekly?.must) || firstLine(saved.weekly?.loose),
        communication: saved.communication?.person || saved.communication?.situation,
        energy: saved.energy?.must || saved.energy?.state,
        decision: saved.decision?.relief || firstLine(saved.decision?.options),
        focus: saved.focus?.task || saved.focus?.mode,
        emotion: saved.emotion?.trigger || saved.emotion?.intensity,
        transition: saved.transition?.transition || saved.transition?.mode,
        appointment: saved.appointment?.provider,
        care: firstLine(saved.care?.main) || firstLine(saved.care?.help),
        support: saved.support?.situation || saved.support?.request,
        workschool: saved.workschool?.challenge || saved.workschool?.setting,
        home: saved.home?.space || saved.home?.mode,
        laundry: saved.laundry?.needed || saved.laundry?.mode,
        digital: saved.digital?.target || saved.digital?.mode,
        money: saved.money?.task || saved.money?.category,
        lost: saved.lost?.item || saved.lost?.type,
        errand: saved.errand?.destination || saved.errand?.kind,
        providers: saved.providers?.length ? String(saved.providers.length) : '',
        access: saved.access?.barrier ? copy.cards.access.title : '',
        task: saved.task?.focus,
        reminders: reminderSummary(saved.reminders),
        tracker: saved.tracker?.date,
    };

    Object.entries(copy.cards).forEach(([key, card]) => {
        const value = summaries[key];
        const cardElement = document.querySelector(`[data-dashboard-card="${key}"]`);
        const statusElement = document.querySelector(`[data-dashboard-status="${key}"]`);
        const summaryElement = document.querySelector(`[data-dashboard-summary="${key}"]`);

        cardElement.classList.toggle('saved', Boolean(value));
        statusElement.textContent = value ? copy.saved : copy.not_saved;
        summaryElement.textContent = value
            ? copy.summaries[key].replace(':value', value)
            : card.empty;
    });

    const next = dashboardNext(saved);
    const nextTitle = document.getElementById('dashboardNextTitle');
    const nextBody = document.getElementById('dashboardNextBody');
    const nextLink = document.getElementById('dashboardNextLink');

    if (! next) {
        nextTitle.textContent = copy.empty_title;
        nextBody.textContent = copy.empty_body;
        nextLink.href = document.querySelector('[data-dashboard-card="goal"] a').href;
        nextLink.textContent = copy.start_link;

        return;
    }

    nextTitle.textContent = copy.cards[next.key].title;
    nextBody.textContent = copy.next[next.key];
    nextLink.href = document.querySelector(`[data-dashboard-card="${next.key}"] a`).href;
    nextLink.textContent = copy.cards[next.key].action;
}

function dashboardNext(saved) {
    if (saved.navigator?.topic || saved.navigator?.knownNext) {
        return { key: 'navigator' };
    }

    if (saved.task?.focus) {
        return { key: 'task' };
    }

    if (saved.reminders?.date) {
        return { key: 'reminders' };
    }

    if (saved.tracker?.date) {
        return { key: 'tracker' };
    }

    if (saved.providers?.length) {
        return { key: 'providers' };
    }

    if (saved.access?.barrier) {
        return { key: 'access' };
    }

    if (saved.weekly?.must || saved.weekly?.loose) {
        return { key: 'weekly' };
    }

    if (saved.planner?.must) {
        return { key: 'planner' };
    }

    if (saved.time?.must || saved.time?.start) {
        return { key: 'time' };
    }

    if (saved.body?.task || saved.body?.food) {
        return { key: 'body' };
    }

    if (saved.meds?.medicine || saved.meds?.mode) {
        return { key: 'meds' };
    }

    if (saved.food?.available || saved.food?.energy) {
        return { key: 'food' };
    }

    if (saved.sleep?.wakeTime || saved.sleep?.mode) {
        return { key: 'sleep' };
    }

    if (saved.motivation?.task || saved.motivation?.reward) {
        return { key: 'motivation' };
    }

    if (saved.accountability?.task || saved.accountability?.person) {
        return { key: 'accountability' };
    }

    if (saved.routine?.kind || saved.routine?.must) {
        return { key: 'routine' };
    }

    if (saved.communication?.person || saved.communication?.situation) {
        return { key: 'communication' };
    }

    if (saved.energy?.must || saved.energy?.state) {
        return { key: 'energy' };
    }

    if (saved.decision?.relief || saved.decision?.options) {
        return { key: 'decision' };
    }

    if (saved.focus?.task || saved.focus?.mode) {
        return { key: 'focus' };
    }

    if (saved.emotion?.trigger || saved.emotion?.intensity) {
        return { key: 'emotion' };
    }

    if (saved.transition?.transition || saved.transition?.mode) {
        return { key: 'transition' };
    }

    if (saved.goal?.goal) {
        return { key: 'goal' };
    }

    if (saved.appointment?.provider) {
        return { key: 'appointment' };
    }

    if (saved.care?.main || saved.care?.help) {
        return { key: 'care' };
    }

    if (saved.support?.situation || saved.support?.request) {
        return { key: 'support' };
    }

    if (saved.workschool?.challenge || saved.workschool?.setting) {
        return { key: 'workschool' };
    }

    if (saved.home?.space || saved.home?.mode) {
        return { key: 'home' };
    }

    if (saved.laundry?.needed || saved.laundry?.mode) {
        return { key: 'laundry' };
    }

    if (saved.digital?.target || saved.digital?.mode) {
        return { key: 'digital' };
    }

    if (saved.money?.task || saved.money?.category) {
        return { key: 'money' };
    }

    if (saved.lost?.item || saved.lost?.type) {
        return { key: 'lost' };
    }

    if (saved.errand?.destination || saved.errand?.kind) {
        return { key: 'errand' };
    }

    if (saved.result?.title) {
        return { key: 'result' };
    }

    return { key: 'resources' };
}

function firstLine(value) {
    return lines(value)[0] || '';
}

function initReminders(tools) {
    const form = document.getElementById('reminderForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.reminders') ?? '{}');
    const fields = {
        kind: document.getElementById('reminderKind'),
        date: document.getElementById('reminderDate'),
        time: document.getElementById('reminderTime'),
        note: document.getElementById('reminderNote'),
    };

    hydrateFields(fields, defaultReminder(saved));
    renderReminder(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.reminders', JSON.stringify(data));
        renderReminder(tools, data);
    });

    document.getElementById('reminderDownloadButton').addEventListener('click', () => {
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.reminders', JSON.stringify(data));
        renderReminder(tools, data);
        downloadText(tools.reminders.filename, reminderToIcs(tools.reminders, data));
    });

    document.getElementById('reminderClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.reminders');
        hydrateFields(fields, defaultReminder({}));
        renderReminder(tools, collectFields(fields));
    });
}

function defaultReminder(saved) {
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);

    return {
        kind: saved.kind || 'book',
        date: saved.date || tomorrow.toISOString().slice(0, 10),
        time: saved.time || '09:00',
        note: saved.note || '',
    };
}

function renderReminder(tools, data) {
    const copy = tools.reminders;
    const kindLabel = copy.kinds[data.kind] || copy.kinds.book;

    document.getElementById('reminderOutputTitle').textContent = copy.calendar_title.replace(':kind', kindLabel);
    document.getElementById('reminderWhenText').textContent = reminderSummary(data);
    document.getElementById('reminderWhyText').textContent = copy.why;
    document.getElementById('reminderScriptText').textContent = reminderScript(copy, data);
}

function reminderSummary(data) {
    if (! data?.date) {
        return '';
    }

    return [data.date, data.time].filter(Boolean).join(' ');
}

function reminderScript(copy, data) {
    const script = copy.scripts[data.kind] || copy.scripts.book;
    const note = data.note?.trim();

    return note ? `${script} ${note}` : script;
}

function reminderToIcs(copy, data) {
    const kindLabel = copy.kinds[data.kind] || copy.kinds.book;
    const start = icsDate(data.date, data.time);
    const end = icsDate(data.date, addMinutes(data.time, 30));
    const title = copy.calendar_title.replace(':kind', kindLabel);
    const description = reminderScript(copy, data);

    return [
        'BEGIN:VCALENDAR',
        'VERSION:2.0',
        'PRODID:-//ADHD Support Hub//Reminder//EN',
        'BEGIN:VEVENT',
        `UID:${calendarUid()}@adhd-support-hub`,
        `DTSTAMP:${icsDateTime(new Date())}`,
        `DTSTART:${start}`,
        `DTEND:${end}`,
        `SUMMARY:${escapeIcs(title)}`,
        `DESCRIPTION:${escapeIcs(description)}`,
        'END:VEVENT',
        'END:VCALENDAR',
        '',
    ].join('\r\n');
}

function calendarUid() {
    if (window.crypto?.randomUUID) {
        return window.crypto.randomUUID();
    }

    return `${Date.now()}-${Math.random().toString(36).slice(2)}`;
}

function addMinutes(time, minutes) {
    const [hours, mins] = (time || '09:00').split(':').map(Number);
    const date = new Date();
    date.setHours(hours || 0, mins || 0, 0, 0);
    date.setMinutes(date.getMinutes() + minutes);

    return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
}

function icsDate(date, time) {
    return `${(date || new Date().toISOString().slice(0, 10)).replaceAll('-', '')}T${(time || '09:00').replace(':', '')}00`;
}

function icsDateTime(date) {
    return date.toISOString().replace(/[-:]/g, '').replace(/\.\d{3}/, '');
}

function escapeIcs(value) {
    return String(value)
        .replaceAll('\\', '\\\\')
        .replaceAll(';', '\\;')
        .replaceAll(',', '\\,')
        .replaceAll('\n', '\\n');
}

function initTracker(tools) {
    const form = document.getElementById('trackerForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.tracker') ?? '{}');
    const fields = {
        date: document.getElementById('trackerDate'),
        sleep: document.getElementById('trackerSleep'),
        focus: document.getElementById('trackerFocus'),
        stress: document.getElementById('trackerStress'),
        context: document.getElementById('trackerContext'),
        notes: document.getElementById('trackerNotes'),
    };

    hydrateFields(fields, defaultTracker(saved));
    renderTracker(tools, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.tracker', JSON.stringify(data));
        renderTracker(tools, data);
    });

    document.getElementById('trackerDownloadButton').addEventListener('click', () => {
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.tracker', JSON.stringify(data));
        renderTracker(tools, data);
        downloadText('adhd-symptom-snapshot.txt', trackerToText(tools, data));
    });

    document.getElementById('trackerClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.tracker');
        hydrateFields(fields, defaultTracker({}));
        renderTracker(tools, collectFields(fields));
    });
}

function defaultTracker(saved) {
    return {
        date: saved.date || new Date().toISOString().slice(0, 10),
        sleep: saved.sleep || '',
        focus: saved.focus || '3',
        stress: saved.stress || '3',
        context: saved.context || '',
        notes: saved.notes || '',
    };
}

function renderTracker(tools, data) {
    const copy = tools.tracker;

    document.getElementById('trackerOutputTitle').textContent = data.date || copy.empty_title;
    document.getElementById('trackerScoresText').textContent = `${copy.labels.focus}: ${data.focus || '3'} · ${copy.labels.stress}: ${data.stress || '3'}`;
    document.getElementById('trackerSleepText').textContent = data.sleep
        ? `${copy.labels.sleep}: ${data.sleep} ${copy.labels.hours}`
        : copy.defaults.sleep;
    document.getElementById('trackerContextText').textContent = data.context || copy.defaults.context;
    document.getElementById('trackerNotesText').textContent = data.notes || copy.defaults.notes;
}

function trackerToText(tools, data) {
    const copy = tools.tracker;

    return [
        `${copy.output_eyebrow}: ${data.date}`,
        '',
        copy.sections.scores,
        `${copy.labels.focus}: ${data.focus || '3'}`,
        `${copy.labels.stress}: ${data.stress || '3'}`,
        '',
        copy.sections.sleep,
        data.sleep ? `${data.sleep} ${copy.labels.hours}` : copy.defaults.sleep,
        '',
        copy.sections.context,
        data.context || copy.defaults.context,
        '',
        copy.sections.notes,
        data.notes || copy.defaults.notes,
    ].join('\n');
}

function hydrateFields(fields, data) {
    Object.entries(fields).forEach(([key, field]) => {
        field.value = data[key] ?? '';
    });
}

function collectFields(fields) {
    return Object.fromEntries(Object.entries(fields).map(([key, field]) => [key, field.value]));
}

function initSupportNavigator(tools, links) {
    const form = document.getElementById('navigatorForm');
    const saved = JSON.parse(supportStorage.getItem('adhdSupport.navigator') ?? '{}');
    const fields = {
        topic: document.getElementById('navigatorTopic'),
        urgency: document.getElementById('navigatorUrgency'),
        energy: document.getElementById('navigatorEnergy'),
        knownNext: document.getElementById('navigatorKnownNext'),
    };

    hydrateFields(fields, {
        topic: 'unsure',
        urgency: 'soon',
        energy: 'medium',
        ...saved,
    });
    renderSupportNavigator(tools, links, collectFields(fields));

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        const data = collectFields(fields);
        supportStorage.setItem('adhdSupport.navigator', JSON.stringify(data));
        renderSupportNavigator(tools, links, data);
    });

    document.getElementById('navigatorPrintButton').addEventListener('click', () => window.print());
    document.getElementById('navigatorDownloadButton').addEventListener('click', () => {
        downloadText('adhd-support-navigator.txt', supportNavigatorToText(tools, collectFields(fields)));
    });
    document.getElementById('navigatorClearButton').addEventListener('click', () => {
        supportStorage.removeItem('adhdSupport.navigator');
        hydrateFields(fields, { topic: 'unsure', urgency: 'soon', energy: 'medium' });
        renderSupportNavigator(tools, links, collectFields(fields));
    });
}

function renderSupportNavigator(tools, links, data) {
    const plan = supportNavigatorPlan(tools.navigator, data);

    document.getElementById('navigatorOutputTitle').textContent = tools.navigator.output_title.replace(':topic', plan.topicLabel);
    document.getElementById('navigatorFirstText').textContent = plan.first;
    renderToolLinks('navigatorRecommendationList', plan.recommendations, tools.navigator.recommendations, links);
    document.getElementById('navigatorWhyText').textContent = plan.why;
    document.getElementById('navigatorStopText').textContent = plan.stop;
}

function supportNavigatorPlan(copy, data) {
    const topic = copy.topic_routes[data.topic] ? data.topic : 'unsure';
    const urgency = data.urgency || 'soon';
    const energy = data.energy || 'medium';
    const knownNext = data.knownNext?.trim() || '';
    let recommendations = [...copy.topic_routes[topic]];

    if (knownNext) {
        recommendations.unshift('planner');
    }

    if (urgency === 'now') {
        recommendations.splice(1, 0, 'transition');
    }

    if (energy === 'low') {
        recommendations.push('energy');
    }

    recommendations = [...new Set(recommendations)].slice(0, 4);

    return {
        topicLabel: copy.topics[topic],
        first: knownNext
            ? copy.first_with_next.replace(':next', knownNext)
            : copy.first_default,
        recommendations,
        why: copy.why
            .replace(':topic', copy.topics[topic])
            .replace(':urgency', copy.urgencies[urgency] || copy.urgencies.soon)
            .replace(':energy', copy.energies[energy] || copy.energies.medium),
        stop: copy.stop_rule,
    };
}

function renderToolLinks(id, keys, copy, links) {
    const container = document.getElementById(id);

    container.innerHTML = '';
    keys.forEach((key) => {
        const item = copy[key];

        if (! item || ! links[key]) {
            return;
        }

        const link = document.createElement('a');
        const title = document.createElement('strong');
        const body = document.createElement('span');

        link.className = 'tool-link-card';
        link.href = links[key];
        title.textContent = item.title;
        body.textContent = item.body;
        link.append(title, body);
        container.appendChild(link);
    });
}

function supportNavigatorToText(tools, data) {
    const copy = tools.navigator;
    const plan = supportNavigatorPlan(copy, data);

    return [
        copy.output_title.replace(':topic', plan.topicLabel),
        '',
        copy.sections.first,
        plan.first,
        '',
        copy.sections.recommended,
        ...plan.recommendations.map((key) => `- ${copy.recommendations[key]?.title || key}: ${copy.recommendations[key]?.body || ''}`),
        '',
        copy.sections.why,
        plan.why,
        '',
        copy.sections.stop,
        plan.stop,
    ].join('\n');
}

function parseClock(value) {
    const match = String(value || '').match(/^(\d{1,2}):(\d{2})$/);

    if (! match) {
        return null;
    }

    const hours = Number(match[1]);
    const minutes = Number(match[2]);

    if (hours > 23 || minutes > 59) {
        return null;
    }

    return (hours * 60) + minutes;
}

function formatClock(value) {
    const minutes = Math.max(0, Math.min(value, 1439));
    const hours = Math.floor(minutes / 60);
    const remainder = minutes % 60;

    return `${String(hours).padStart(2, '0')}:${String(remainder).padStart(2, '0')}`;
}

function lines(value) {
    return (value || '')
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);
}

function withDefault(items, fallback) {
    return items.length > 0 ? items : [fallback];
}

function renderList(id, items, fallback) {
    const list = document.getElementById(id);

    list.innerHTML = '';
    withDefault(items, fallback).forEach((item) => {
        const listItem = document.createElement('li');
        listItem.textContent = item;
        list.appendChild(listItem);
    });
}

function downloadText(filename, contents) {
    const blob = new Blob([contents], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);
}
