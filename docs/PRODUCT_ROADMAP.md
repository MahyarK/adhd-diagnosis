# ADHD Support Hub Product Roadmap

## Vision

Build a free, compassionate ADHD support hub for people who cannot easily afford help, delay looking for help, forget to follow up, or feel too overwhelmed to start. The app should help a user move from “I might have ADHD” to “I have a clear next step, a printable plan, and tools that make today easier.”

This is not a replacement for diagnosis, therapy, coaching, or emergency care. It is a practical bridge to support.

## Primary Users

- People who suspect ADHD but have not been evaluated yet.
- People with ADHD who procrastinate, forget follow-up, or lose track of support resources.
- People with limited money, insurance access, time, executive function, or local mental health access.
- Students, workers, parents, and adults who need simple tools they can use immediately.
- Users who need multilingual support: English, Dutch, French, and Persian.

## Product Principles

- Low shame: never blame the user for forgetting, delaying, or struggling.
- Low friction: every tool should have a tiny first step.
- Offline-friendly outputs: printable plans, downloads, and exports matter.
- Privacy-first: sensitive data should be optional, explainable, and easy to delete.
- Action over education-only: every page should answer “what can I do next?”
- Accessible language: no medical knowledge required.
- ADHD-friendly UX: short sections, clear hierarchy, progress feedback, collapsible details, and visible next actions.

## Core App Areas

### 1. Screening And Understanding

Current foundation:

- ADHD screening wizard.
- Presentation result: inattentive, hyperactive/impulsive, combined, low signal, or needs more context.
- Multilingual result explanations.
- Nearby clinician map CTA.

Next improvements:

- Add a printable result report.
- Add “what to bring to a clinician” checklist.
- Add “questions to ask at the appointment.”
- Add a “save my result” option for logged-in users or local-only storage.
- Add emergency/non-emergency guidance copy for crisis situations.

### 2. Goal Builder

Purpose:

Help users convert vague life problems into small, doable goals.

Features:

- Goal templates: school, work, home, money, health, relationships, admin, cleaning, sleep.
- “Make it smaller” button that breaks a goal into tiny steps.
- “First 2 minutes” action generator.
- Difficulty selector: low energy, normal, urgent, crisis cleanup.
- Goal cards with:
  - title
  - why it matters
  - next physical action
  - blockers
  - support needed
  - deadline or reminder
- Export goal as PDF.
- Print goal card.
- Download as `.txt` or `.json`.

MVP:

- One goal form.
- Auto-generated action plan.
- Print/download button.

### 3. Planning And Routines

Purpose:

Give users planning tools that work with ADHD instead of assuming perfect consistency.

Features:

- Daily “minimum viable day” planner.
- Weekly reset planner.
- Morning/evening routine builder.
- Time-block planner with buffers.
- “If I fall off” recovery plan.
- Home reset planner for cleaning, clutter, dishes, trash, and leaving-home routines.
- Money/admin rescue planner for bills, forms, emails, appointments, and overdue tasks.
- Communication repair planner for late replies, conflict, boundaries, and asking for support.
- Energy crash rescue planner for burnout, low-capacity days, and “I cannot do the whole day” moments.
- Decision rescue planner for choosing one good-enough priority when everything feels urgent.
- Focus sprint planner with timer, body-double support, distraction parking, and reward.
- Emotional reset planner for shame spirals, rejection sensitivity, guilt, conflict, and overwhelm.
- Visual checklist mode.
- Printable planner pages.
- Downloadable weekly plan.

MVP:

- Daily planner with 3 priorities, appointments, body needs, one scary task, and recovery note.
- Print and download.

### 4. Task Breakdown Tools

Purpose:

Reduce overwhelm by turning tasks into visible, tiny actions.

Current foundation:

- Task breakdown worksheet with brain dump, microsteps, hidden steps, and print/download.

Features:

- Brain dump capture box.
- Sort tasks into now/later/waiting/delegate.
- Break one task into microsteps.
- Identify hidden steps.
- Add timers: 5, 10, 15, 25 minutes.
- Body double prompt: “tell someone what you are about to do.”
- “Done enough” definition.

MVP:

- Brain dump plus microstep generator using local rule-based prompts.
- Save task breakdown locally.

### 5. Follow-Up And Reminders

Purpose:

Help users remember help-seeking and self-support steps.

Current foundation:

- Reminder worksheet with common follow-up types.
- Local saved reminder shown on the dashboard.
- Downloadable `.ics` calendar reminder.

Features:

- Reminders for:
  - booking clinician appointment
  - preparing documents
  - taking notes to appointment
  - checking insurance or low-cost options
  - following up after appointment
- Browser/local reminders first.
- Optional email reminders later.
- “Remind me again because I forgot” button.
- No-shame reminder copy.

MVP:

- Local downloadable calendar `.ics` reminders.

### 6. Clinician And Support Finder

Current foundation:

- Browser location CTA.
- OpenStreetMap/Overpass provider listings.
- Google Maps fallback.
- Appointment prep worksheet with print/download.
- Call, email, and in-person scripts for asking about ADHD evaluation.
- Provider shortlist with cost/access/status notes and download.
- Low-cost access plan for cost, waitlists, referrals, and fallback options.
- Support request builder for work, school, home, or trusted helpers.

Next improvements:

- Filters:
  - psychologist
  - psychiatrist
  - therapist
  - coach
  - general practitioner
  - clinic
  - low-cost/free
  - telehealth
- Add “call script” and “email script.”
- Add country-specific resource pages.

Important limitation:

Map data cannot guarantee all providers. The app must always say listings may be incomplete and users should verify licensure, ADHD experience, cost, and availability.

### 7. Resource Library

Purpose:

Give users high-quality ADHD-friendly tools without requiring deep research.

Current foundation:

- Resource library page with reputable public-health/nonprofit source links.
- Practical “one thing to try today” prompt on each resource card.

Categories:

- Understanding ADHD.
- Getting evaluated.
- Low-cost help.
- Work/school accommodations.
- Sleep and ADHD.
- Emotional regulation.
- Procrastination.
- Money/admin.
- Cleaning/home reset.
- Relationships and communication.

Content format:

- Short explanation.
- Why it matters.
- One thing to try today.
- Printable worksheet when useful.
- Links to reputable sources.

MVP:

- 10 core articles with printable worksheets.

### 8. Printable And Downloadable Toolkit

Purpose:

Make the app useful even if the user leaves, loses internet, or needs paper.

Downloads:

- Screening result report.
- Clinician appointment prep sheet.
- Daily planner.
- Weekly reset planner.
- Goal card.
- Task breakdown sheet.
- Medication/therapy discussion notes.
- Work/school/home support request draft.
- Home reset card.
- Money/admin rescue card.
- Communication repair draft.
- Energy crash rescue card.
- Decision rescue priority card.
- Focus sprint card.
- Emotional reset card.
- Symptom tracker.
- Sleep/stress tracker.

MVP:

- Browser print styles for result, goal, daily planner, appointment prep, and task breakdown.
- Download `.txt` summaries.

### 9. Personal Dashboard

Purpose:

Create a gentle home base after screening.

Current foundation:

- Local dashboard that summarizes saved screening result, goal, plan, appointment prep, and task breakdown.
- “Next tiny step” prompt based on what the user has saved.
- Downloadable local support kit export.
- Clear local support kit action.
- Saved weekly reset, follow-up reminder, and symptom tracker summary.

Sections:

- Today’s tiny next step.
- Saved goals.
- Current plan.
- Upcoming reminders.
- Provider shortlist.
- Recent wins.
- Tools library shortcuts.

MVP:

- Local-storage dashboard with saved plans before adding accounts.

### 10. Accounts, Data, And Privacy

Recommended path:

1. Start with no account required.
2. Store drafts locally in the browser.
3. Let users export everything.
4. Add optional accounts later for cross-device use.

Privacy requirements:

- Explain what is stored.
- Allow delete/export.
- Avoid collecting location until the user clicks the map CTA.
- Do not store precise location unless absolutely necessary.
- Do not sell or share health data.
- Clearly state the app is informational and supportive, not diagnostic.

## Suggested Build Phases

### Phase 1: Make The Current Result Actionable

- Printable result report.
- Download result summary.
- Clinician prep checklist.
- Call/email scripts.
- Save result locally.

### Phase 2: Add Goal Builder And Daily Planner

- Goal builder.
- Daily planner.
- Print/download for both.
- Local storage dashboard.

### Phase 3: Add Task Breakdown And Reminder Tools

- Brain dump and microstep task splitter.
- `.ics` reminder download.
- Weekly reset planner.

### Phase 4: Expand Support Finder

- Provider filters.
- Shortlist providers.
- Low-cost resource links.
- Country-specific support pages.

### Phase 5: Add Accounts And Durable Storage

- Optional user accounts.
- Saved goals/plans/results.
- Export/delete data.
- Email reminders if users opt in.

## First Implementation Slice

Build this next:

1. Add a post-result action panel with:
   - Print result
   - Download result
   - Create first goal
   - Build today’s plan
   - Prepare appointment
2. Add browser print styles for the result report.
3. Add a goal builder page.
4. Add a daily planner page.
5. Add a time-block planner with buffers and recovery space.
6. Add a body needs check-in for food, water, medication routine, sleep, movement, pain, and low-capacity moments.
7. Add a food rescue tool for low-energy, low-appetite, low-money, and forgot-to-eat moments.
8. Add a motivation menu for free/low-cost rewards, safe stimulation, and boring-task activation.
9. Add an accountability check-in for body doubling, proof of progress, and missed-check-in recovery.
10. Add an appointment prep page with scripts and a checklist.
11. Add a task breakdown page with brain dump and microsteps.
12. Add follow-up reminders with downloadable `.ics` files.
13. Add a weekly reset planner for recovering from falling behind.
14. Add a symptom/context tracker for clinician-ready notes.
15. Add a starter resource library for trustworthy low-friction next steps.
16. Add a provider shortlist for possible clinicians, clinics, and support offices.
17. Add a low-cost access plan for care barriers.
18. Add care discussion notes and a support request builder.
19. Add a home reset tool for cleaning and clutter overwhelm.
20. Add a money/admin rescue tool for bills, forms, messages, appointments, insurance/cost questions, and overdue tasks.
21. Add a lost item rescue tool for keys, wallet, phone, documents, medicine, and prevention spots.
22. Add a morning/evening/leaving-home routine builder.
23. Add a communication repair tool for late replies, conflict, boundaries, and support asks.
24. Add an energy crash rescue tool for burnout, overwhelm, and low-capacity days.
25. Add a decision rescue tool for choosing one good-enough priority and parking the rest.
26. Add a focus sprint tool for starting work with a timer, body-double support, distraction parking, and a reward.
27. Add an emotional reset tool for shame spirals, rejection sensitivity, guilt, conflict, and overwhelm.
28. Add a transition rescue tool for waiting mode, leaving home, switching tasks, bedtime, and frozen-before-start moments.
29. Save goals/plans/time blocks/body needs/food rescues/motivation menus/accountability check-ins/routines/weekly reset/communication repair/energy crash rescue/decision rescue/focus sprint/emotional reset/transition rescue/home reset/money-admin rescue/lost item rescue/appointment prep/care notes/support requests/provider shortlist/access plan/task breakdown/reminders/tracker to local storage.

This slice directly supports users who procrastinate, forget, or cannot afford immediate professional support.

## Success Criteria

- A user can screen themselves and understand the result in plain language.
- A user can find potential local support without manually researching from scratch.
- A user can create one realistic goal and print/download it.
- A user can create a daily plan and print/download it.
- A user can create time blocks with buffers and print/download them.
- A user can create a body needs check-in card and print/download it.
- A user can create a food rescue card and print/download it.
- A user can create a motivation menu and print/download it.
- A user can create an accountability check-in card and print/download it.
- A user can create a small routine card and print/download it.
- A user can create a weekly reset plan and print/download it.
- A user can draft a communication repair script and print/download it.
- A user can create an energy crash rescue card and print/download it.
- A user can create a decision rescue priority card and print/download it.
- A user can create a focus sprint card and print/download it.
- A user can create an emotional reset card and print/download it.
- A user can create a transition rescue card and print/download it.
- A user can create a small home reset plan and print/download it.
- A user can create a money/admin rescue card and print/download it.
- A user can create a lost item rescue card and print/download it.
- A user can prepare what to say to a clinician and print/download it.
- A user can draft a practical support request for work, school, home, or a trusted helper.
- A user can break an overwhelming task into tiny steps and print/download it.
- A user can create a calendar reminder for follow-up.
- A user can track symptoms, sleep, stress, and context for a clinician-ready snapshot.
- A user can open a small resource library without researching from scratch.
- A user can save and export possible providers to contact.
- A user can make a low-cost access plan for care barriers.
- A user can return to a local dashboard and export saved support-kit data.
- A user can leave the app with at least one concrete next action.
- The app remains free to use without requiring an account.
- Sensitive data is optional and exportable.
