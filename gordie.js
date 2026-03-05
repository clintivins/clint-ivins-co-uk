/**
 * Gordie the ID Guy - Floating Chat Widget
 * Auto-injects onto any page. Just include this script.
 */
(function () {
    // Prevent double-init
    if (window.__gordieInit) return;
    window.__gordieInit = true;

    // ===== INJECT CSS =====
    const style = document.createElement('style');
    style.textContent = `
        /* Gordie Floating Trigger Button */
        #gordie-trigger {
            position: fixed;
            bottom: 28px;
            right: 28px;
            z-index: 99999;
            background: linear-gradient(135deg, #bd00ff, #7b00e0);
            border: none;
            border-radius: 50%;
            width: 64px;
            height: 64px;
            cursor: pointer;
            box-shadow: 0 4px 25px rgba(189, 0, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s, box-shadow 0.3s;
            animation: gordie-bounce 2s ease infinite;
        }
        #gordie-trigger:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 35px rgba(189, 0, 255, 0.7);
        }
        #gordie-trigger .gordie-face {
            font-size: 30px;
            line-height: 1;
        }
        #gordie-trigger .gordie-label {
            position: absolute;
            right: 72px;
            background: rgba(15,15,25,0.95);
            border: 1px solid rgba(189,0,255,0.4);
            color: #fff;
            padding: 6px 14px;
            border-radius: 8px;
            font-family: 'Outfit', 'Segoe UI', sans-serif;
            font-size: 0.85rem;
            white-space: nowrap;
            pointer-events: none;
            opacity: 1;
            transition: opacity 0.3s;
        }
        #gordie-trigger .gordie-label::after {
            content: '';
            position: absolute;
            right: -6px;
            top: 50%;
            transform: translateY(-50%);
            border: 6px solid transparent;
            border-left-color: rgba(15,15,25,0.95);
        }
        #gordie-trigger.gordie-open .gordie-label { opacity: 0; pointer-events: none; }

        @keyframes gordie-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        #gordie-trigger:hover { animation: none; }
        #gordie-trigger.gordie-open { animation: none; }

        /* Pulse ring */
        #gordie-trigger .gordie-pulse {
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            border: 2px solid rgba(189,0,255,0.6);
            animation: gordie-pulse-ring 2s ease-out infinite;
        }
        @keyframes gordie-pulse-ring {
            0% { transform: scale(1); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }

        /* Chat Container */
        #gordie-chat {
            position: fixed;
            bottom: 100px;
            right: 28px;
            width: 380px;
            max-height: 520px;
            z-index: 99998;
            background: rgba(10, 10, 20, 0.97);
            border: 1px solid rgba(189,0,255,0.35);
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 10px 50px rgba(0,0,0,0.7), 0 0 30px rgba(189,0,255,0.15);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transform: scale(0) translateY(20px);
            transform-origin: bottom right;
            opacity: 0;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s;
            pointer-events: none;
            font-family: 'Outfit', 'Segoe UI', sans-serif;
        }
        #gordie-chat.gordie-visible {
            transform: scale(1) translateY(0);
            opacity: 1;
            pointer-events: auto;
        }

        /* Header */
        #gordie-chat .g-header {
            background: linear-gradient(135deg, rgba(189,0,255,0.2), rgba(120,0,200,0.1));
            padding: 14px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(189,0,255,0.2);
        }
        #gordie-chat .g-header-title {
            color: #bd00ff;
            font-weight: 700;
            font-size: 0.95rem;
            font-family: 'Space Mono', monospace;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        #gordie-chat .g-close {
            background: none;
            border: none;
            color: rgba(255,255,255,0.5);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 4px;
            transition: color 0.2s;
        }
        #gordie-chat .g-close:hover { color: #fff; }

        /* Login Overlay */
        #gordie-chat .g-login {
            position: absolute;
            inset: 0;
            background: rgba(10,10,20,0.98);
            z-index: 10;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            gap: 12px;
        }
        #gordie-chat .g-login.g-hidden { display: none; }
        #gordie-chat .g-login i { font-size: 2.5rem; color: #bd00ff; }
        #gordie-chat .g-login h3 { color: #fff; margin: 0; font-size: 1.1rem; }
        #gordie-chat .g-login p { color: rgba(255,255,255,0.5); margin: 0; font-size: 0.85rem; }
        #gordie-chat .g-login input {
            width: 100%;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            color: #fff;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.2s;
        }
        #gordie-chat .g-login input:focus { border-color: #bd00ff; }
        #gordie-chat .g-login button {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #bd00ff, #7b00e0);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        #gordie-chat .g-login button:hover { opacity: 0.85; }
        #gordie-chat .g-login-error {
            color: #ff4444;
            font-size: 0.8rem;
            display: none;
        }

        /* Messages */
        #gordie-chat .g-messages {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 250px;
            max-height: 340px;
        }
        #gordie-chat .g-messages::-webkit-scrollbar { width: 4px; }
        #gordie-chat .g-messages::-webkit-scrollbar-thumb { background: rgba(189,0,255,0.3); border-radius: 4px; }

        #gordie-chat .g-msg {
            max-width: 90%;
            padding: 10px 14px;
            border-radius: 12px;
            font-size: 0.88rem;
            line-height: 1.5;
            word-wrap: break-word;
        }
        #gordie-chat .g-msg.bot {
            align-self: flex-start;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(0,243,255,0.15);
            color: rgba(255,255,255,0.9);
        }
        #gordie-chat .g-msg.user {
            align-self: flex-end;
            background: linear-gradient(135deg, #bd00ff, #9000d0);
            color: #fff;
        }
        #gordie-chat .g-msg.system {
            align-self: flex-start;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(0,243,255,0.12);
            color: rgba(255,255,255,0.7);
        }
        #gordie-chat .g-msg a {
            color: #00f3ff;
            text-decoration: underline;
        }

        /* Input */
        #gordie-chat .g-input-area {
            display: flex;
            padding: 10px 12px;
            border-top: 1px solid rgba(255,255,255,0.08);
            gap: 8px;
        }
        #gordie-chat .g-input-area input {
            flex: 1;
            padding: 10px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            color: #fff;
            font-size: 0.9rem;
            outline: none;
        }
        #gordie-chat .g-input-area input:focus { border-color: rgba(189,0,255,0.5); }
        #gordie-chat .g-input-area button {
            background: linear-gradient(135deg, #bd00ff, #7b00e0);
            border: none;
            border-radius: 10px;
            color: #fff;
            padding: 0 14px;
            cursor: pointer;
            font-size: 1rem;
            transition: opacity 0.2s;
        }
        #gordie-chat .g-input-area button:hover { opacity: 0.85; }

        /* === Gordie Thinking Panel === */
        /* === Gordie Reasoning Applet (Separate Window) === */
        #gordie-reasoning-applet {
            position: fixed;
            bottom: 100px;
            right: 430px; /* Positions it to the left of the main chat container (which is 380px wide + 28px offset) */
            width: 320px;
            max-height: 520px;
            z-index: 99997;
            background: linear-gradient(135deg, rgba(0, 18, 8, 0.98), rgba(0, 12, 6, 0.98));
            border: 1px solid rgba(0, 255, 100, 0.25);
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 10px 50px rgba(0,0,0,0.8), 0 0 30px rgba(0,255,100,0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            transform: translateX(40px) scale(0.95);
            transform-origin: right bottom;
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s;
            pointer-events: none;
            font-family: 'Space Mono', 'Courier New', monospace;
        }
        #gordie-reasoning-applet.g-applet-visible {
            transform: translateX(0) scale(1);
            opacity: 1;
            pointer-events: auto;
        }
        #gordie-reasoning-applet::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(0,255,100,0.6), transparent);
        }
        #gordie-chat .g-thinking-panel {
            padding: 14px;
            flex: 1;
            display: flex;
            flex-direction: column;
            color: rgba(0, 255, 100, 0.85);
            font-size: 0.75rem;
            line-height: 1.6;
            overflow-y: auto;
        }
        #gordie-chat .g-thinking-panel::-webkit-scrollbar { width: 4px; }
        #gordie-chat .g-thinking-panel::-webkit-scrollbar-thumb { background: rgba(0,255,100,0.3); border-radius: 4px; }
        #gordie-chat .g-thinking-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(0,255,100,0.15);
            color: rgba(0, 255, 100, 0.95);
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        #gordie-chat .g-thinking-spinner {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 2px solid rgba(0,255,100,0.25);
            border-top-color: rgba(0,255,100,0.9);
            border-radius: 50%;
            animation: g-spin 0.6s linear infinite;
        }
        @keyframes g-spin { to { transform: rotate(360deg); } }
        #gordie-chat .g-thinking-steps-container {
            margin-bottom: 14px;
        }
        #gordie-chat .g-thinking-step {
            opacity: 0;
            transform: translateX(-10px);
            transition: opacity 0.35s ease, transform 0.35s ease;
            padding: 4px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
        }
        #gordie-chat .g-thinking-step.visible { opacity: 1; transform: translateX(0); }
        #gordie-chat .g-thinking-step.done { color: rgba(0,255,100,0.5); }
        #gordie-chat .g-thinking-step .step-check { color: #00ff64; font-weight: 700; }
        #gordie-chat .g-cursor-line { padding: 4px 0; }
        #gordie-chat .g-cursor {
            display: inline-block;
            width: 8px;
            height: 14px;
            background: rgba(0,255,100,0.8);
            animation: g-cursor-blink 0.6s steps(1) infinite;
        }
        @keyframes g-cursor-blink { 0%,50%{opacity:1} 51%,100%{opacity:0} }
        #gordie-chat .g-thinking-panel.complete .g-thinking-spinner {
            animation: none;
            border-color: #00ff64;
            background: #00ff64;
        }
        #gordie-chat .g-reasoning-content {
            flex: 1;
            color: rgba(0,255,100,0.6);
            font-size: 0.7rem;
            line-height: 1.6;
            white-space: pre-wrap;
            word-break: break-word;
            border-top: 1px dashed rgba(0,255,100,0.15);
            padding-top: 12px;
            margin-top: 10px;
            display: none;
            opacity: 0;
            transition: opacity 0.5s ease;
        }
        #gordie-chat .g-reasoning-content.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 480px) {
            #gordie-reasoning-applet {
                top: 20px;
                right: 12px;
                bottom: calc(100vh - 90px); /* Tries to fit above chat on mobile. But hiding it is safer on small screens */
                width: calc(100vw - 24px);
                max-height: 40vh;
                display: none; /* Mobile space is tight, hiding sidebar applet or layering it is tricky, hiding for now */
            }
        }
    `;
    document.head.appendChild(style);

    // ===== INJECT HTML =====

    // Trigger button
    const trigger = document.createElement('button');
    trigger.id = 'gordie-trigger';
    trigger.title = 'Chat with Gordie';
    trigger.innerHTML = `
        <span class="gordie-face">😊</span>
        <span class="gordie-pulse"></span>
        <span class="gordie-label">Need help? Ask Gordie!</span>
    `;
    document.body.appendChild(trigger);

    // Reasoning Applet container
    const reasoningApplet = document.createElement('div');
    reasoningApplet.id = 'gordie-reasoning-applet';
    reasoningApplet.innerHTML = `<div class="g-thinking-panel" id="g-thinking-panel"></div>`;
    document.body.appendChild(reasoningApplet);

    // Chat container
    const chat = document.createElement('div');
    chat.id = 'gordie-chat';
    chat.innerHTML = `
        <div class="g-login" id="g-login">
            <i class="fas fa-user-shield"></i>
            <h3>Access Restricted</h3>
            <p>Identify yourself to Gordie</p>
            <input type="text" id="g-username" placeholder="Username" autocomplete="off">
            <input type="password" id="g-password" placeholder="Password">
            <button id="g-login-btn">Authenticate</button>
            <div class="g-login-error" id="g-login-error">Invalid credentials.</div>
        </div>
        <div class="g-header">
            <div class="g-header-title">😊 GORDIE</div>
            <button class="g-close" id="g-close" title="Close"><i class="fas fa-minus"></i></button>
        </div>
        <div class="g-messages" id="g-messages">
            <div class="g-msg system">Hey there dude! 🤙 Gordie here — your cyber-savvy surfer bro. Ask me anything about identity security, MITRE ATT&CK, or the latest threats!</div>
        </div>
        <div class="g-input-area">
            <input type="text" id="g-input" placeholder="Ask Gordie anything..." autocomplete="off">
            <button id="g-send"><i class="fas fa-paper-plane"></i></button>
        </div>
    `;
    document.body.appendChild(chat);

    // ===== LOGIC =====
    let isOpen = false;
    let isAuth = false;
    const msgArea = document.getElementById('g-messages');
    const input = document.getElementById('g-input');
    const sendBtn = document.getElementById('g-send');
    const loginOverlay = document.getElementById('g-login');
    const usernameInput = document.getElementById('g-username');
    const passwordInput = document.getElementById('g-password');
    const loginBtn = document.getElementById('g-login-btn');
    const loginError = document.getElementById('g-login-error');
    const closeBtn = document.getElementById('g-close');

    // Toggle
    trigger.onclick = () => {
        isOpen = !isOpen;
        chat.classList.toggle('gordie-visible', isOpen);
        trigger.classList.toggle('gordie-open', isOpen);
        if (!isOpen) {
            reasoningApplet.classList.remove('g-applet-visible');
        }
        if (isOpen && !isAuth) usernameInput.focus();
        if (isOpen && isAuth) input.focus();
    };

    closeBtn.onclick = () => {
        isOpen = false;
        chat.classList.remove('gordie-visible');
        trigger.classList.remove('gordie-open');
        reasoningApplet.classList.remove('g-applet-visible');
    };

    // Login
    const handleLogin = () => {
        if (usernameInput.value.trim() === 'Gandolf' && passwordInput.value.trim() === '@@Thunder_55') {
            isAuth = true;
            loginOverlay.classList.add('g-hidden');
            loginError.style.display = 'none';
            input.focus();
        } else {
            loginError.style.display = 'block';
            passwordInput.value = '';
        }
    };
    loginBtn.onclick = handleLogin;
    [usernameInput, passwordInput].forEach(el => {
        el.onkeypress = (e) => { if (e.key === 'Enter') handleLogin(); };
    });

    // Markdown renderer
    function renderMarkdown(text) {
        return text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\[([^\]]+)\]\((https?:\/\/[^\)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>')
            .replace(/^### (.+)$/gm, '<strong style="color: #00f3ff; font-size: 0.95rem; display:block; margin-top:0.7rem;">$1</strong>')
            .replace(/^#### (.+)$/gm, '<strong style="color: #ffd700; font-size: 0.9rem; display:block; margin-top:0.5rem;">$1</strong>')
            .replace(/^---$/gm, '<hr style="border:none; border-top:1px solid rgba(255,255,255,0.12); margin:0.6rem 0;">')
            .replace(/^[\u2022\-\*] (.+)$/gm, '<span style="display:block; padding-left:0.8rem; text-indent:-0.5rem;">• $1</span>')
            .replace(/^\d+\. (.+)$/gm, function (m) { return '<span style="display:block; padding-left:0.8rem;">' + m + '</span>'; })
            .replace(/\n/g, '<br>');
    }

    // Add message
    function addMsg(text, sender) {
        const div = document.createElement('div');
        div.className = 'g-msg ' + sender;
        div.innerHTML = sender === 'user' ? text : renderMarkdown(text);
        msgArea.appendChild(div);
        msgArea.scrollTop = msgArea.scrollHeight;
    }

    // === Agentic Reasoning Streaming ===
    function getThinkingSteps(query) {
        const q = query.toLowerCase();
        const steps = [
            { icon: '⚡', agent: 'Orchestrator', text: 'New task received. Analyzing intent and extracting entities...' },
            { icon: '🧠', agent: 'Orchestrator', text: `Query parsed: "${query.length > 30 ? query.substring(0, 30) + '...' : query}"` },
            { icon: '📋', agent: 'Orchestrator', text: 'Generating execution plan: 1) Identify concepts 2) Gather intel 3) Synthesize.' }
        ];

        let hasSpecificIntel = false;

        if (/mitre|att&ck|technique|tactic/.test(q)) {
            hasSpecificIntel = true;
            steps.push({ icon: '📡', agent: 'Orchestrator', text: 'Dispatching Sub-Task --> [Intel-Agent]: "Retrieve MITRE ATT&CK framework data"' });
            steps.push({ icon: '🗂️', agent: 'Intel-Agent', text: 'Connecting to attack.mitre.org API... Extracted technique signatures.' });
        }
        if (/cve|vulnerabilit|exploit|patch|zero.?day/.test(q)) {
            hasSpecificIntel = true;
            steps.push({ icon: '📡', agent: 'Orchestrator', text: 'Dispatching Sub-Task --> [Vuln-Agent]: "Check latest CVE databases"' });
            steps.push({ icon: '🔎', agent: 'Vuln-Agent', text: 'Scanning nvd.nist.gov and cve.mitre.org. Cross-referencing exploits.' });
        }
        if (/ransomware|malware|apt|threat.?actor|campaign/.test(q)) {
            hasSpecificIntel = true;
            steps.push({ icon: '📡', agent: 'Orchestrator', text: 'Dispatching Sub-Task --> [ThreatRecon]: "Identify active campaigns"' });
            steps.push({ icon: '🦠', agent: 'ThreatRecon', text: 'Querying external intel pipelines (mandiant, crowdstrike) for IOCs.' });
        }
        if (/identity|oauth|saml|oidc|sso|mfa|entra|okta|fido|passkey/.test(q)) {
            hasSpecificIntel = true;
            steps.push({ icon: '📡', agent: 'Orchestrator', text: 'Dispatching Sub-Task --> [IdM-Agent]: "Analyze identity protocol vectors"' });
            steps.push({ icon: '🔐', agent: 'IdM-Agent', text: 'Mapping identity surfaces (OAuth/OIDC/SAML) and authentication bypass logic.' });
        }
        if (/phish|social.?engineer|credential|password|brute/.test(q)) {
            hasSpecificIntel = true;
            steps.push({ icon: '📡', agent: 'Orchestrator', text: 'Dispatching Sub-Task --> [SecOps-Agent]: "Review phishing/credential vectors"' });
            steps.push({ icon: '🎣', agent: 'SecOps-Agent', text: 'Analyzing credential harvesting and social engineering tactics.' });
        }

        if (!hasSpecificIntel) {
            steps.push({ icon: '📡', agent: 'Orchestrator', text: 'Dispatching Sub-Task --> [Knowledge-Base]: "Retrieve general cybersecurity context"' });
        }

        steps.push({ icon: '🌐', agent: 'Orchestrator', text: 'Dispatching Sub-Task --> [Search-Agent]: "Execute live Web Search for latest context"' });
        steps.push({ icon: '🔍', agent: 'Search-Agent', text: 'Fetching real-time data to ground response...' });

        steps.push({ icon: '🧠', agent: 'Orchestrator', text: 'All sub-tasks completed. Passing data payloads to [Logic-Agent].' });
        steps.push({ icon: '🧵', agent: 'Logic-Agent', text: 'Synthesizing data payloads into final reasoning chain...' });
        steps.push({ icon: '✅', agent: 'Formatting-Agent', text: 'Formatting final response for user...' });

        return steps;
    }

    function resetThinkingPanel(query) {
        reasoningApplet.classList.add('g-applet-visible');
        const panel = document.getElementById('g-thinking-panel');
        // Store steps for animation
        panel._stepsForAnimation = getThinkingSteps(query);

        panel.innerHTML = `
            <div class="g-thinking-header" id="g-thinking-header">
                <span class="g-thinking-spinner"></span>
                AGENTIC REASONING STREAM
            </div>
            <div class="g-thinking-steps-container" id="g-thinking-steps-container"></div>
            <div class="g-cursor-line" id="g-cursor-line"><span class="g-cursor"></span></div>
            <div class="g-reasoning-content" id="g-reasoning-content"></div>
        `;
        panel.classList.remove('complete');
        return panel;
    }

    function animateThinkingSteps(panel) {
        const steps = panel._stepsForAnimation || [];
        const container = document.getElementById('g-thinking-steps-container');
        if (!container) return;

        let stepIndex = 0;
        let charIndex = 0;
        let currentStepEl = null;
        let currentText = "";

        // Fast typer interval
        const interval = setInterval(() => {
            if (stepIndex >= steps.length) {
                // Done with initial steps
                clearInterval(interval);
                return;
            }

            const step = steps[stepIndex];

            // Start of a new step
            if (charIndex === 0) {
                if (currentStepEl) {
                    currentStepEl.classList.add('done');
                    const ico = currentStepEl.querySelector('.step-check');
                    if (ico) ico.textContent = '✓';
                }
                currentText = `[${step.agent}] ${step.text}`;
                currentStepEl = document.createElement('div');
                currentStepEl.className = 'g-thinking-step visible';
                currentStepEl.innerHTML = `<span class="step-check">${step.icon}</span> <span class="step-text"></span>`;
                container.appendChild(currentStepEl);
            }

            // Typewriter effect
            const textSpan = currentStepEl.querySelector('.step-text');
            if (textSpan && charIndex < currentText.length) {
                // Type a few characters at a time for speed simulation
                const charsToType = Math.floor(Math.random() * 3) + 2;
                textSpan.textContent += currentText.substring(charIndex, charIndex + charsToType);
                charIndex += charsToType;
                panel.scrollTop = panel.scrollHeight;
            } else {
                // Let it pause slightly at the end of a line
                charIndex = 0;
                stepIndex++;
            }

        }, 40); // Fast typing speed

        panel._interval = interval;
    }

    function completeThinking(panel, thinkingText, sources) {
        if (panel._interval) clearInterval(panel._interval);

        // Complete the last active step
        const stepEls = panel.querySelectorAll('.g-thinking-step');
        if (stepEls.length > 0) {
            const last = stepEls[stepEls.length - 1];
            last.classList.add('done');
            const ico = last.querySelector('.step-check');
            if (ico) ico.textContent = '✓';
        }

        // If we have actual sources from the backend, inject a "Target confirmed" step dynamically
        if (sources && sources.length > 0) {
            const container = document.getElementById('g-thinking-steps-container');
            const domains = sources.map(s => {
                try { return new URL(s.url).hostname; } catch (e) { return s.title; }
            });
            if (container) {
                const stepEl = document.createElement('div');
                stepEl.className = 'g-thinking-step visible done';
                stepEl.innerHTML = `<span class="step-check">✓</span> <span class="step-text">[Intel-Agent] Grounding sources verified: ${domains.join(', ')}</span>`;
                container.appendChild(stepEl);
            }
        }

        const cursor = document.getElementById('g-cursor-line');
        if (cursor) cursor.remove();

        panel.classList.add('complete');
        const hdr = document.getElementById('g-thinking-header');
        if (hdr) hdr.innerHTML = '<span class="g-thinking-spinner"></span> STREAM COMPLETE';

        if (thinkingText && thinkingText.trim()) {
            const content = document.getElementById('g-reasoning-content');
            if (content) {
                content.textContent = "=== AI INTERNAL LOGIC STATE ===\n" + thinkingText.trim();
                content.classList.add('show');
                setTimeout(() => { panel.scrollTop = panel.scrollHeight; }, 100);
            }
        }
    }

    // Send
    async function handleSend() {
        if (!isAuth) return;
        const q = input.value.trim();
        if (!q) return;
        addMsg(q, 'user');
        input.value = '';

        const thinkPanel = resetThinkingPanel(q);
        // Show loading state in chat too so it feels responsive
        const loadId = 'g-load-' + Date.now();
        const ldiv = document.createElement('div');
        ldiv.className = 'g-msg bot';
        ldiv.id = loadId;
        ldiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Gordie is working with the logic engine...';
        msgArea.appendChild(ldiv);
        msgArea.scrollTop = msgArea.scrollHeight;

        animateThinkingSteps(thinkPanel);

        try {
            const res = await fetch('mitre_bot.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: q })
            });
            const data = await res.json();
            completeThinking(thinkPanel, data.thinking || '', data.sources || []);
            const el = document.getElementById(loadId);
            if (el) el.remove();

            if (data.status === 'success') {
                addMsg(data.message, 'bot');
            } else {
                addMsg(data.message || 'Connection error.', 'system');
            }
        } catch (e) {
            completeThinking(thinkPanel, '');
            const el = document.getElementById(loadId);
            if (el) el.remove();
            addMsg('Uplink failed. Check your connection.', 'system');
        }
    }

    sendBtn.onclick = handleSend;
    input.onkeypress = (e) => { if (e.key === 'Enter') handleSend(); };

    // Remove old bot elements from dashboard if they exist (prevent duplicates)
    const oldTrigger = document.getElementById('intel-bot-trigger');
    const oldContainer = document.getElementById('intel-bot-container');
    if (oldTrigger) oldTrigger.style.display = 'none';
    if (oldContainer) oldContainer.style.display = 'none';

})();
