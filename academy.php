<?php
// T3chN0mad Academy Backend - Leaderboard & Session Management
session_start();

$leaderboardFile = 'leaderboard.json';

// Initialize leaderboard if it doesn't exist
if (!file_exists($leaderboardFile)) {
    file_put_contents($leaderboardFile, json_encode([]));
}

// Handle Leaderboard Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_score') {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $score = filter_var($_POST['score'], FILTER_VALIDATE_INT);
    $rating = filter_var($_POST['rating'], FILTER_SANITIZE_STRING);
    $time = date('Y-m-d H:i:s');

    if ($name && $score !== false) {
        $leaderboard = json_decode(file_get_contents($leaderboardFile), true);
        $leaderboard[] = [
            'name' => $name,
            'score' => $score,
            'rating' => $rating,
            'date' => $time
        ];

        // Sort by score (desc) then date
        usort($leaderboard, function ($a, $b) {
            if ($b['score'] === $a['score']) {
                return strtotime($b['date']) - strtotime($a['date']);
            }
            return $b['score'] - $a['score'];
        });

        // Keep top 50
        $leaderboard = array_slice($leaderboard, 0, 50);
        file_put_contents($leaderboardFile, json_encode($leaderboard));

        echo json_encode(['status' => 'success']);
        exit;
    }
}

// Handle Fetch Leaderboard
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_leaderboard') {
    echo file_get_contents($leaderboardFile);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="T3chN0mad Academy - Advanced MITRE ATT&CK Identity Challenge. 25 Tough Scenarios.">
    <title>Cyber Academy | T3chN0mad</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Space+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=2.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .academy-hero {
            height: 60vh;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-image: url('assets/images/academy.png');
            background-size: cover;
            background-position: center;
            position: relative;
            margin-top: 80px;
        }

        .game-container {
            max-width: 1000px;
            margin: -100px auto 100px;
            padding: 2rem;
            position: relative;
            z-index: 10;
        }

        .glass-card {
            background: rgba(15, 15, 25, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        }

        /* Leaderboard Styles */
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
            color: var(--text-main);
            font-family: 'Outfit', sans-serif;
        }

        .leaderboard-table th {
            text-align: left;
            padding: 1rem;
            border-bottom: 2px solid var(--neon-cyan);
            color: var(--neon-cyan);
            font-family: 'Space Mono', monospace;
            text-transform: uppercase;
        }

        .leaderboard-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--glass-border);
        }

        .leaderboard-table tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .top-three {
            color: var(--neon-gold);
            font-weight: 800;
        }

        /* Quiz Specifics */
        .option-btn {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            padding: 1.2rem;
            border-radius: 10px;
            color: white;
            text-align: left;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1rem;
            display: flex;
            gap: 1rem;
        }

        .option-btn:hover {
            background: rgba(0, 243, 255, 0.12);
            border-color: var(--neon-cyan);
            transform: translateX(10px);
        }

        .rank-badge-large {
            font-size: 3.5rem;
            font-family: 'Space Mono', monospace;
            text-shadow: 0 0 20px currentColor;
            margin: 1.5rem 0;
        }

        .input-glow {
            width: 100%;
            padding: 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 10px;
            color: white;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .input-glow:focus {
            border-color: var(--neon-cyan);
            box-shadow: 0 0 15px rgba(0, 243, 255, 0.2);
        }
    </style>
</head>

<body>
    <nav class="glass-nav">
        <div class="logo">T3ch<span class="highlight">N0mad</span></div>
        <ul class="nav-links">
            <li><a href="index.html">Home</a></li>
            <li><a href="academy.php" class="active">Academy</a></li>
            <li><a href="dashboard.html">Threat Intel</a></li>
            <li><a href="contact.php" class="btn-glow">Connect</a></li>
        </ul>
    </nav>

    <section class="academy-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1 class="glitch" data-text="ELITE DEFENDER">ELITE DEFENDER</h1>
            <p class="subtitle">The Identity Gauntlet: 25 Scenarios</p>
        </div>
    </section>

    <main class="game-container">
        <div class="glass-card">
            <!-- Phase 1: Registration -->
            <div id="reg-screen">
                <div style="text-align: center; margin-bottom: 2rem;">
                    <i class="fas fa-fingerprint"
                        style="font-size: 4rem; color: var(--neon-purple); filter: drop-shadow(0 0 15px var(--neon-purple));"></i>
                    <h2>Initialize Identity</h2>
                </div>
                <p style="text-align: center; color: var(--text-muted); margin-bottom: 2rem;">Enter your callsign to
                    begin the assessment. Your ranking will be recorded in the global leaderboard.</p>
                <input type="text" id="user-name" class="input-glow" placeholder="Operator Callsign..." maxlength="20">
                <button class="btn-glow" id="start-btn" style="width: 100%; padding: 1.5rem; font-size: 1.2rem;">BEGIN
                    GAUNTLET</button>
            </div>

            <!-- Phase 2: Quiz -->
            <div id="quiz-screen" style="display: none;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span id="question-count" style="font-family: 'Space Mono', monospace; color: var(--neon-cyan);">SCR
                        01/25</span>
                    <span id="player-display" style="color: var(--text-muted);">OP: --</span>
                </div>
                <div class="progress-container" style="height: 4px; margin-bottom: 2rem;">
                    <div class="progress-bar" id="game-progress" style="width: 0%;"></div>
                </div>

                <div id="scenario-container">
                    <p id="scenario-text"
                        style="font-size: 1.2rem; line-height: 1.6; margin-bottom: 2rem; background: rgba(255,255,255,0.03); padding: 2rem; border-left: 4px solid var(--neon-purple);">
                    </p>
                    <div id="options-container"></div>
                </div>

                <div id="feedback-area"
                    style="display: none; padding: 1.5rem; margin-top: 1.5rem; border-radius: 10px; border: 1px solid transparent;">
                    <h4 id="feedback-title" style="margin-bottom: 0.5rem;"></h4>
                    <p id="feedback-desc" style="font-size: 0.95rem; margin-bottom: 1rem;"></p>
                    <button class="btn-outline" id="next-btn" style="width: 100%;">CONTINUE</button>
                </div>
            </div>

            <!-- Phase 3: Results & Leaderboard -->
            <div id="result-screen" style="display: none; text-align: center;">
                <h2>Gauntlet Results</h2>
                <div style="display: flex; justify-content: center; align-items: baseline; gap: 1rem; margin: 2rem 0;">
                    <span id="final-score" style="font-size: 5rem; font-weight: 800; color: white;">0</span>
                    <span style="font-size: 2rem; color: var(--text-muted);">/ 25</span>
                </div>
                <div id="rank-badge" class="rank-badge-large"></div>
                <p id="rank-desc" style="color: var(--text-muted); margin-bottom: 3rem;"></p>

                <h3
                    style="text-align: left; border-bottom: 1px solid var(--glass-border); padding-bottom: 1rem; margin-bottom: 1rem;">
                    <i class="fas fa-trophy" style="color: var(--neon-gold);"></i> GLOBAL LEADERBOARD
                </h3>
                <div style="max-height: 400px; overflow-y: auto;">
                    <table class="leaderboard-table">
                        <thead>
                            <tr>
                                <th>Pos</th>
                                <th>Operator</th>
                                <th>Score</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-body"></tbody>
                    </table>
                </div>

                <div style="margin-top: 3rem; display: flex; gap: 1rem; justify-content: center;">
                    <button class="btn-glow" onclick="location.reload()">RE-INITIALIZE</button>
                    <a href="dashboard.html" class="btn-outline">SITUATION MAP</a>
                </div>
            </div>
        </div>
    </main>

    <script>
        const scenarios = [
            { q: "A high-privileged user account logs in from a server used for backups. The process 'tar' makes DNS requests to a known fast-flux domain.", o: ["The backup script is compromised; isolate the server.", "This is normal OS data exfiltration.", "Rotate password and wait.", "Whitelist the domain."], a: 0, e: "Attackers often hijack service accounts. Sudden DNS traffic from a backup tool is a major IOC." },
            { q: "A developer's laptop Forensic analysis shows access to 'shadow' files on a VM using a SSH key found in a '.env' file.", o: ["Notify the developer.", "Revoke SSH key and rotate all credentials.", "Enable MFA.", "Mark .env as read-only."], a: 1, e: "Secrets in source code are critical. Leaked keys require full rotation of all related credentials." },
            { q: "You notice a 'Golden Ticket' attack: a TGT with a lifetime of 10 years has been used to access the Domain Controller.", o: ["Extend lifetime.", "Reset KRBTGT password twice immediately.", "Reboot DC.", "Disable the user account."], a: 1, e: "Resetting KRBTGT twice is the only way to invalidate existing Golden Tickets." },
            { q: "An AWS IAM role assigned to an EC2 instance has 'AdministratorAccess'. Logs show 'sts:AssumeRole' from unknown IP.", o: ["Restrict to Least Privilege.", "Delete instance.", "Change root password.", "Nothing, regular cloud behavior."], a: 0, e: "Over-privileged roles allow attackers to pivot from instance metadata theft into the whole cloud." },
            { q: "A user's Entra ID shows a login from a TOR exit node, but MFA was completed via SMS.", o: ["Likely vacation VPN.", "SMS MFA intercepted or SIM-swapped.", "Safe since MFA passed.", "TOR is standard business."], a: 1, e: "SMS MFA is vulnerable to interception and SIM swapping, especially via TOR." },
            { q: "Attacker uses 'NTLM Relay' to capture admin session on a file server without SMB Signing.", o: ["Enable SMB Signing.", "Switch to longer passwords.", "Move to cloud.", "Enforce NTLMv2 only."], a: 0, e: "SMB Signing prevents relay attacks by cryptographically signing sessions." },
            { q: "lsass.exe memory is dumped to 'debug.bin' via 'NotAVirus.exe'.", o: ["Isolate host; credential dumping.", "Check signature.", "Delete file later.", "Add to exclusion list."], a: 0, e: "lsass dumping is a classic way to steal credentials from memory." },
            { q: "A database service account is seen logging into a workstation via RDP.", o: ["Normal admin behavior.", "Restrict service accounts from interactive login.", "Increase disk quota.", "Assign to RDP users."], a: 1, e: "Service accounts should never be allowed interactive (RDP/Console) login." },
            { q: "A user reports receiving multiple MFA push notifications and hits 'Approve' to stop them.", o: ["MFA Fatigue attack; reset password.", "System malfunction.", "Ignore; only one approval.", "User is being proactive."], a: 0, e: "Pestering a user with MFA prompts until they approve is a common social engineering tactic." },
            { q: "Kerberoasting attack detected (Event 4769) requesting RC4 encryption for high-value account.", o: ["Enable AES and strengthen password.", "Switch to NTLM.", "Rename account.", "Grant Domain Admin."], a: 0, e: "Kerberoasting targets weak service account passwords that use legacy encryption." },
            { q: "Attacker uses 'Pass-the-Hash' to move between workstations. Best long-term fix?", o: ["Enforce LAPS.", "Longer passwords.", "Re-image weekly.", "Hide Admin from login."], a: 0, e: "LAPS ensures every workstation has a unique, rotating local admin password." },
            { q: "A hidden LDAP query enumerates 'Domain Admins' every 5 minutes.", o: ["Mapping for lateral movement.", "Standard health check.", "LDAP server load.", "User checking permissions."], a: 0, e: "Frequent enumeration of high-value groups is a sign of an attacker mapping targets." },
            { q: "Silver Ticket suspected because a specific service is accessed without a TGT request record.", o: ["Service account compromised.", "DC is offline.", "User has Super Admin.", "Reset all domain passwords."], a: 0, e: "Silver Tickets are forged using a specific service account's NTLM hash, bypassing the DC." },
            { q: "A user's browser profile is synced to attacker cloud storage. Main risk?", o: ["Theft of session cookies and saved passwords.", "Seeing bookmarks.", "Slower browser.", "No risk; data is encrypted."], a: 0, e: "Syncing browser profiles allows attackers to steal active sessions (AiTM) and saved secrets." },
            { q: "AWS S3 bucket is 'Publicly Readable' and contains 'config.json' with API keys.", o: ["Revoke keys and secure bucket.", "Standard dev sharing.", "Faster API access.", "Encrypt file with password."], a: 0, e: "Public S3 buckets are a massive source of leaked infrastructure secrets." },
            { q: "Attacker uses 'GPP' password decryption on an XML file in SYSVOL.", o: ["Attacker obtained local admin password.", "SYSVOL is corrupt.", "GPP is very secure.", "Windows 11 is immune."], a: 0, e: "Old GPP files in SYSVOL often contain encrypted passwords with a publicly known key." },
            { q: "Attacker uses 'sudo' tokens remaining in memory to execute as root.", o: ["Reduce sudo caching timeout.", "Delete sudo.", "Doesn't matter; already root.", "Use su instead."], a: 0, e: "Abusing a valid sudo session in another terminal is a common local privilege escalation." },
            { q: "Sticky Keys: Pressing SHIFT 5 times at login screen opens Command Prompt as SYSTEM.", o: ["sethc.exe replaced with cmd.exe.", "Hidden recovery feature.", "Keyboard is faulty.", "Expert Windows shortcut."], a: 0, e: "The Sticky Keys binary replacement is a classic Windows persistence method." },
            { q: "Attacker modifies 'AuthorizedKeys' file in a user's home directory.", o: ["Established SSH-based persistence.", "Fixing login issues.", "SSH keys are always safer.", "File sharing setup."], a: 0, e: "Adding a key to AuthorizedKeys allows the attacker to log in as the user anytime via SSH." },
            { q: "What is required for a 'Pass-the-Ticket' attack?", o: ["A valid Kerberos ticket file.", "Plaintext password.", "Hardware token.", "Fingerprints."], a: 0, e: "Pass-the-ticket relies on using a stolen TGT or Service Ticket, no password needed." },
            { q: "Attacker uses a 'Malicious Service' to run a payload as SYSTEM at boot.", o: ["Persistence via System Process modification.", "Standard Windows update.", "Services are always safe.", "Delete Services.msc."], a: 0, e: "Creating or modifying services is a highly effective way for attackers to maintain access." },
            { q: "User identity stolen via 'Adversary-in-the-Middle' proxying MFA page.", o: ["Use FIDO2/WebAuthn keys.", "Switch to email MFA.", "Change password.", "MFA is useless now."], a: 0, e: "AiTM can proxy SMS and App codes, but cannot spoof a physical FIDO2 hardware handshake." },
            { q: "What is 'IDaaS' (Identity as a Service) exploitation focused on?", o: ["Stealing/forging authentication tokens.", "Cracking server BIOS.", "Reading physical drives.", "Bypassing office locks."], a: 0, e: "IDaaS attacks target the tokens (JWT/SAML) used to prove identity in the cloud." },
            { q: "Mass 'Account Lockout' event (500 users in 1 minute).", o: ["Brute Force: Password Spraying.", "AD database error.", "Password Change day.", "Firewall blockage."], a: 0, e: "Spraying common passwords across many users at once is a quiet alternative to brute forcing one user." },
            { q: "Attacker uses 'BloodHound' on a network.", o: ["Discovery and path analysis for movement.", "Professional security game.", "Speeding up network.", "AD self-repairing."], a: 0, e: "BloodHound uses graph theory to find complex attack paths that admins often miss." }
        ];

        // SHUFFLE the options so they aren't always in the same place
        scenarios.forEach(s => {
            const correctText = s.o[s.a];
            s.o.sort(() => Math.random() - 0.5);
            s.a = s.o.indexOf(correctText);
        });

        // SHUFFLE the scenarios as well
        let shuffledScenarios = [...scenarios];
        shuffledScenarios.sort(() => Math.random() - 0.5);
        const finalScenarios = shuffledScenarios.slice(0, 25);

        let currentScrenario = 0;
        let score = 0;
        let playerName = "";

        const regScreen = document.getElementById('reg-screen');
        const quizScreen = document.getElementById('quiz-screen');
        const resultScreen = document.getElementById('result-screen');
        const userNameInput = document.getElementById('user-name');
        const startBtn = document.getElementById('start-btn');
        const scenarioText = document.getElementById('scenario-text');
        const optionsContainer = document.getElementById('options-container');
        const feedbackArea = document.getElementById('feedback-area');
        const qCount = document.getElementById('question-count');
        const playerDisplay = document.getElementById('player-display');
        const progressBar = document.getElementById('game-progress');

        startBtn.onclick = () => {
            playerName = userNameInput.value.trim();
            if (!playerName) { alert("Identify yourself, Operator."); return; }
            regScreen.style.display = 'none';
            quizScreen.style.display = 'block';
            playerDisplay.innerText = `OP: ${playerName.toUpperCase()}`;
            loadScenario();
        };

        function loadScenario() {
            const data = finalScenarios[currentScrenario];
            qCount.innerText = `SCR ${String(currentScrenario + 1).padStart(2, '0')}/25`;
            scenarioText.innerText = data.q;
            optionsContainer.innerHTML = '';
            feedbackArea.style.display = 'none';
            progressBar.style.width = `${(currentScrenario / 25) * 100}%`;

            data.o.forEach((opt, idx) => {
                const btn = document.createElement('button');
                btn.className = 'option-btn';
                btn.innerHTML = `<span>[${String.fromCharCode(65 + idx)}]</span> ${opt}`;
                btn.onclick = () => checkAnswer(idx);
                optionsContainer.appendChild(btn);
            });
        }

        function checkAnswer(idx) {
            const data = finalScenarios[currentScrenario];
            const btns = optionsContainer.querySelectorAll('.option-btn');
            btns.forEach(b => b.style.pointerEvents = 'none');

            feedbackArea.style.display = 'block';
            if (idx === data.a) {
                score++;
                btns[idx].style.borderColor = "#00ff88";
                btns[idx].style.background = "rgba(0,255,136,0.1)";
                document.getElementById('feedback-title').innerText = "✓ THREAT NEUTRALIZED";
                document.getElementById('feedback-title').style.color = "#00ff88";
            } else {
                btns[idx].style.borderColor = "#ff4444";
                btns[data.a].style.borderColor = "#00ff88";
                document.getElementById('feedback-title').innerText = "✗ PERIMETER BREACHED";
                document.getElementById('feedback-title').style.color = "#ff4444";
            }
            document.getElementById('feedback-desc').innerText = data.e;
        }

        document.getElementById('next-btn').onclick = () => {
            currentScrenario++;
            if (currentScrenario < 25) {
                loadScenario();
            } else {
                finishGame();
            }
        };

        async function finishGame() {
            quizScreen.style.display = 'none';
            resultScreen.style.display = 'block';
            document.getElementById('final-score').innerText = score;

            let rank = "";
            let color = "";
            let desc = "";

            if (score === 25) { rank = "CYBER WARLORD"; color = "#00f3ff"; desc = "Legendary. You are the wall that cannot be breached."; }
            else if (score >= 20) { rank = "ELITE HANDLER"; color = "#bd00ff"; desc = "Exceptional. Your tactical knowledge is master-tier."; }
            else if (score >= 15) { rank = "SENIOR ANALYST"; color = "#ffd700"; desc = "Impressive. You manage the frontier with precision."; }
            else if (score >= 10) { rank = "FIELD AGENT"; color = "#ffffff"; desc = "Competent, but the dark web has more tricks up its sleeve."; }
            else { rank = "VULNERABLE TARGET"; color = "#ff4444"; desc = "Critical lack of identity awareness. You are a risk to the network."; }

            const badge = document.getElementById('rank-badge');
            badge.innerText = rank;
            badge.style.color = color;
            document.getElementById('rank-desc').innerText = desc;

            try {
                await fetch('academy.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=submit_score&name=${encodeURIComponent(playerName)}&score=${score}&rating=${encodeURIComponent(rank)}`
                });
            } catch (e) {
                console.error("Leaderboard submission failed", e);
            }

            loadLeaderboard();
        }

        async function loadLeaderboard() {
            try {
                const resp = await fetch('academy.php?action=get_leaderboard');
                const data = await resp.json();
                const body = document.getElementById('leaderboard-body');
                body.innerHTML = '';

                data.forEach((entry, idx) => {
                    const row = `<tr>
                        <td>${idx + 1}</td>
                        <td class="${idx < 3 ? 'top-three' : ''}">${entry.name}</td>
                        <td>${entry.score}/25</td>
                        <td style="font-size: 0.8rem; font-family: 'Space Mono';">${entry.rating}</td>
                    </tr>`;
                    body.innerHTML += row;
                });
            } catch (e) {
                console.error("Leaderboard loading failed", e);
            }
        }
    </script>
</body>

</html>