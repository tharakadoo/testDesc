# Claude Agent SDK: Multi-Step Workflows

## Installation

```bash
# Python
pip install claude-agent-sdk

# TypeScript
npm install @anthropic-ai/claude-agent-sdk
```

## Model Selection Per Agent

```python
from claude_agent_sdk import ClaudeAgentOptions, AgentDefinition

agents = {
    "planner": AgentDefinition(
        description="Creates plans",
        prompt="Generate detailed plans",
        model="haiku",  # Cheap for planning
        tools=["Read", "Glob", "Grep"]
    ),
    "implementer": AgentDefinition(
        description="Implements solutions",
        prompt="Implement the solution",
        model="sonnet",  # Quality for coding
        tools=["Read", "Write", "Edit", "Bash"]
    )
}
```

## Approval Gate

```python
from claude_agent_sdk.types import PermissionResultAllow, PermissionResultDeny

async def approval_gate(tool_name: str, input_data: dict, context):
    # Auto-approve reads
    if tool_name in ["Read", "Glob", "Grep"]:
        return PermissionResultAllow(updated_input=input_data)

    # Ask for writes
    print(f"\n⚠️  {tool_name}: {input_data}")
    response = input("Approve? (y/n): ").lower()

    if response == "y":
        return PermissionResultAllow(updated_input=input_data)
    return PermissionResultDeny(message="Rejected by user")
```

## Complete Workflow Example

```python
import asyncio
from claude_agent_sdk import ClaudeSDKClient, ClaudeAgentOptions, AgentDefinition
from claude_agent_sdk.types import PermissionResultAllow, PermissionResultDeny, HookMatcher

async def approval_gate(tool_name, input_data, context):
    if tool_name in ["Read", "Glob", "Grep", "AskUserQuestion"]:
        return PermissionResultAllow(updated_input=input_data)

    print(f"\n[{tool_name}] {input_data}")
    if input("Approve? (y/n): ").lower() == "y":
        return PermissionResultAllow(updated_input=input_data)
    return PermissionResultDeny(message="Rejected")

async def dummy_hook(input_data, tool_use_id, context):
    return {"continue_": True}

async def main():
    agents = {
        "planner": AgentDefinition(
            description="Creates plans",
            prompt="Analyze and create implementation plan",
            model="haiku",
            tools=["Read", "Glob", "Grep"]
        ),
        "implementer": AgentDefinition(
            description="Implements solutions",
            prompt="Implement the approved plan",
            model="sonnet",
            tools=["Read", "Write", "Edit", "Bash"]
        )
    }

    options = ClaudeAgentOptions(
        agents=agents,
        allowed_tools=["Read", "Glob", "Grep", "Task", "Write", "Edit", "Bash"],
        can_use_tool=approval_gate,
        hooks={"PreToolUse": [HookMatcher(matcher=None, hooks=[dummy_hook])]}
    )

    async with ClaudeSDKClient(options=options) as client:
        # Step 1: Plan (Haiku)
        await client.query("Use planner agent to create a plan for: [your task]")
        async for msg in client.receive_response():
            print(msg)

        # Step 2: Approve
        if input("\nImplement? (y/n): ").lower() != "y":
            return

        # Step 3: Implement (Sonnet)
        await client.query("Use implementer agent to execute the plan")
        async for msg in client.receive_response():
            print(msg)

asyncio.run(main())
```

## Key Points

1. **Haiku for planning** - cheap, fast, good for exploration
2. **Sonnet for implementation** - quality code output
3. **`canUseTool`** - approval gates before actions
4. **`ClaudeSDKClient`** - maintains context across steps
5. **Agents inherit tools** - restrict tools per agent for safety
