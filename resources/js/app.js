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

        document.getElementById('nextSteps').innerHTML = result.next_steps
            .slice(0, 3)
            .map((step) => `<li>${step}</li>`)
            .join('');
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

    renderQuestion();
}
