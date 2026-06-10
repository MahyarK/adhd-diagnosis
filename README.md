# ADHD Screening Companion

A gentle Laravel web app for ADHD self-screening. It asks DSM-5-informed questions in a non-attacking way, helps users reflect on inattentive and hyperactive/impulsive patterns, and suggests which ADHD presentation may be worth discussing with a clinician.

This is a screening and preparation tool, not a medical diagnosis.

## Features

- One-question-at-a-time wizard flow
- Compact no-scroll layout for desktop and mobile
- Small answer buttons with auto-advance
- Gamified progress with focus points and checkpoints
- Questions for inattentive symptoms, hyperactive/impulsive symptoms, childhood onset, duration, multiple settings, impairment, masking, family pattern, and possible overlapping causes
- Server-side scoring for inattentive, hyperactive/impulsive, combined, lower-signal, and needs-more-context results
- Clear next steps for clinician follow-up
- Local support dashboard for saved results and tool outputs
- Support navigator that recommends the right tool when the user feels stuck or overloaded
- Goal builder, daily planner, time blocks, body needs check-in, medication refill rescue, food rescue, sleep wind-down, motivation menu, accountability check-in, routine builder, weekly reset, communication repair, energy crash rescue, decision rescue, focus sprint, emotional reset, transition rescue, home reset, laundry rescue, digital clutter rescue, money/admin rescue, lost item rescue, errand launch, appointment prep, care notes, support request, work/school support menu, and task breakdown worksheets
- Follow-up reminder tool with downloadable calendar files
- Symptom/context tracker for clinician-ready notes
- Starter resource library with reputable public-health/nonprofit links
- Provider shortlist for possible clinicians, clinics, and support offices
- Low-cost access plan for cost, waitlists, referrals, and fallback options
- Print/download support for practical follow-through
- English, Dutch, French, and Persian language support

## Product Direction

The long-term goal is to grow this into a free ADHD support hub for people who cannot easily afford help, procrastinate looking for help, or forget follow-up steps. Planned modules include goal creation, printable and downloadable plans, daily planning, task breakdown, reminders, clinician search support, and a practical resource library.

See [docs/PRODUCT_ROADMAP.md](docs/PRODUCT_ROADMAP.md) for the full product roadmap and phased implementation plan.

## Tech Stack

- Laravel 13
- PHP 8.3+
- Vite
- Tailwind CSS
- Vanilla JavaScript
- SQLite by default

## Getting Started

Install dependencies:

```bash
composer install
npm install
```

Create the environment file and app key:

```bash
cp .env.example .env
php artisan key:generate
```

Create the SQLite database if needed:

```bash
touch database/database.sqlite
php artisan migrate
```

Build frontend assets:

```bash
npm run build
```

Run the app:

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

## Development

Run Vite during frontend work:

```bash
npm run dev
```

Run tests:

```bash
php artisan test
```

## Important Note

ADHD diagnosis requires a qualified clinician to evaluate symptoms, history, impairment, settings, development, and other possible explanations. This app is designed to help users organize their experience and decide whether professional evaluation may be useful.
