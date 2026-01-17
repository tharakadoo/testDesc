# Workflow: Plan with Haiku, Execute with Sonnet

## Model Cost Reference
1. **Haiku** - Cheapest, fastest
2. **Sonnet** - Mid-tier, balanced
3. **Opus** - Most expensive, highest quality
excl
## Token-Efficient Workflow

### Phase 1: Planning & Discovery → Use Haiku
- Analyzing requirements
- Exploring codebase (file reads, searches)
- Identifying files that need changes
- Creating implementation plans
- Proposing test strategies

**Why Haiku:** Planning involves many file reads and searches that consume tokens. These tasks don't need complex reasoning - just discovery and documentation.

### Phase 2: Implementation → Use Sonnet
- Writing code
- Creating test files
- Making edits to existing files
- Running and fixing tests

**Why Sonnet:** Code generation benefits from higher quality reasoning. Worth the extra cost for actual implementation.

## How to Apply

When starting multi-step tasks:
1. Use `model: "haiku"` for Task agents doing exploration/planning
2. Switch to `model: "sonnet"` for Task agents doing implementation
3. Or manually specify: "Use haiku for planning, sonnet for execution"

## Example
```
User: "Analyze the codebase and create a plan to add feature X"
→ Use Haiku (exploration + planning)

User: "Implement the plan"
→ Use Sonnet (code writing)
```
