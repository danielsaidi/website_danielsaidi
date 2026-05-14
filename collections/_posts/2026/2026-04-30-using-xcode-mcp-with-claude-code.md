---
title:  Using Xcode MCP with Claude Code
date:   2026-04-30 08:00:00 +0100
tags:   xcode ai

image-show: 0
image: /assets/blog/26/0430/image.jpg

bleeping: https://bleepingswift.com
setup: https://bleepingswift.com/blog/xcode-mcp-server-ai-workflow
autoapprove: https://bleepingswift.com/blog/auto-approve-xcode-mcp-connection

toot: https://mastodon.social/@danielsaidi/116574186078049253
bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3mlteuq7bus2h
---

Xcode 26.3 ships with a built-in MCP server that lets AI assistants like Claude Code understand your Xcode projects, trigger builds, run tests, etc., without you having to paste errors between tools.


## Background

I've been using Claude Code for a while, and while it's been great at reading and editing Swift files, it has felt disconnected from Xcode. It could see my source code files, but had no idea how my project was structured, which targets existed, or what build errors I was getting. 

When Apple added a built-in MCP server to Xcode 26.3, it changed the game. MCP (Model Context Protocol) is an open standard that lets AI tools interact with external systems, and with Xcode now supporting it, Claude Code can query project hierarchies, trigger builds, run tests, and much more.

Getting everything set up smoothly took some learning, though, where two articles from [Bleeping Swift](https://bleepingswift.com) helped me a lot along the way. I'm happy to share them here, in case it helps you too.


## Setting up Xcode MCP

The first article I found was [How to Use Xcode's MCP Server to Build Xcode into Your AI Workflow]({{page.setup}}), which shows how to connect Claude Code to Xcode's MCP server. 

The setup itself is straightforward. You simply enable MCP in Xcode's settings under the Intelligence tab, then register the server with Claude Code by running:

```
claude mcp add --transport stdio xcode -- xcrun mcpbridge
```

What I found most valuable about this article was the explanation of what the MCP server actually gives you. It also discusses adding project-specific context to a `CLAUDE.md` file, like scheme names, test targets, and any build quirks. Well worth a read!


## Auto-approving the MCP connection dialog

Once I had MCP set up, I ran into an annoyance that almost made me stop using it. Every once in a while, Xcode would pop up a dialog asking me to allow the MCP connection. This got old fast.

That's where the second article came in. [Auto-Approve the Xcode MCP Connection Prompt with Claude Code Hooks]({{page.autoapprove}}) shows you how to use Claude Code's hook system to automatically dismiss the dialog whenever a new session starts, using a JavaScript for Automation (JXA) script that checks if Xcode is running, looks for the MCP permission dialog, and clicks "Allow" programmatically.

This will hopefully be fixed in future Xcode versions, but if you also struggle with it, the article is a great read.


## Conclusion

These articles helped me setting up a smooth, integrated workflow where Claude Code and Xcode work together without friction. Well, it's still early days and there are still annoyances, but the MCP server approach sure looks promising.

If you're using Claude Code for Swift development, I highly recommend reading both articles. Big thanks to [Bleeping Swift]({{page.bleeping}}) for sharing! 