# Safepay Hosted Redirect Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the broken embedded Safepay/Flex payment step with a same-tab redirect to Safepay Hosted Checkout and return the shopper to local success/failure pages.

**Architecture:** Keep payment initialization in `SafePayService`, but stop generating capture context. Instead, create a tracker, mint a Safepay passport token, construct a hosted checkout URL, and return that URL from the existing checkout API. On return, lightweight Inertia pages fetch tracker status from a new API endpoint keyed by the Safepay tracker token.

**Tech Stack:** Laravel 13, Inertia React, Safepay HTTP APIs, Vite

## Global Constraints

- Do not add, update, or run tests unless the user explicitly asks for tests.
- Prefer small, focused edits that match the existing structure.
- Use lightweight verification only for touched files unless the user asks for more.

---
