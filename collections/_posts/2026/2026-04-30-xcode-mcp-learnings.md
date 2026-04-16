---
title:  Learning to use Xcode MCP with Claude Code
date:   2026-04-30 08:00:00 +0100
tags:   swift xcode ai mcp

setup: https://bleepingswift.com/blog/xcode-mcp-server-ai-workflow
autoapprove: https://bleepingswift.com/blog/auto-approve-xcode-mcp-connection
---

Xcode's built-in MCP server lets AI assistants like Claude Code understand your Xcode projects, trigger builds, run tests, and report diagnostics without you having to copy-paste errors between tools. Here's how two articles from Bleeping Swift helped me get started.


## Background

I've been using Claude Code for a while now, and while it's been great at reading and editing Swift files, it has always felt disconnected from Xcode. It could see my source files, but it had no idea how my project was structured, which targets existed, or what build errors I was getting. Every time a build failed, I had to manually copy the error back into Claude Code before it could help me fix it.

When Apple added a built-in MCP server to Xcode, it changed the game. MCP (Model Context Protocol) is an open standard that lets AI tools interact with external systems, and with Xcode supporting it, Claude Code can now query project hierarchies, trigger builds, run tests, and get structured compiler diagnostics — all without leaving the terminal. Getting it all set up smoothly took some learning, though, and two articles from [Bleeping Swift](https://bleepingswift.com) helped me a lot along the way.


## Setting up Xcode MCP

The first article I found was [Xcode MCP Server for AI Workflows]({{page.setup}}), which walks through how to connect Claude Code to Xcode's MCP server. The setup itself is straightforward — you enable MCP in Xcode's settings under the Intelligence tab, then register the server with Claude Code by running:

```
claude mcp add --transport stdio xcode -- xcrun mcpbridge
```

What I found most valuable about this article wasn't just the setup steps, but the explanation of what the MCP server actually gives you. Before reading it, I thought of MCP as a vague "AI integration" feature. The article made it concrete — once connected, Claude Code can query your project's target structure, trigger scheme-specific builds, run tests and get structured results, and read compiler errors with file paths and line numbers. It turns Claude Code from a smart text editor into something that actually understands your Xcode project.

The article also has a great tip about adding project-specific context to a `CLAUDE.md` file, like your scheme names, test targets, and any build quirks. This small addition makes a big difference in how effectively Claude Code can work with your project.


## Auto-approving the MCP connection dialog

Once I had MCP set up, I ran into an annoyance that almost made me stop using it. Every time I started a new Claude Code session or ran `/clear`, Xcode would pop up a permission dialog asking me to allow the MCP connection. During active development, where I might clear sessions several times an hour, this got old fast.

That's where the second article came in — [Auto-Approve Xcode MCP Connection]({{page.autoapprove}}). It uses Claude Code's hook system to automatically dismiss the dialog whenever a new session starts. The solution is a JavaScript for Automation (JXA) script that checks if Xcode is running, looks for the MCP permission dialog, and clicks "Allow" programmatically. You then wire it up as a `SessionStart` hook in your Claude Code settings.

There's one thing to be aware of — your terminal app needs accessibility permissions for the script to interact with Xcode's UI, and the auto-approval only works when Xcode is already running. The first connection after launching Xcode still requires a manual click. But after that, it's completely seamless.


## Conclusion

These two articles took me from being curious about Xcode MCP to having a smooth, integrated workflow where Claude Code and Xcode work together without friction. The MCP server gives Claude Code the project awareness it was always missing, and the auto-approval hook removes the one annoyance that could have made me abandon the setup.

If you're using Claude Code for Swift development, I highly recommend reading both articles and setting this up. It makes a real difference in how productive you can be.