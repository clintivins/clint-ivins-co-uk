document.addEventListener('DOMContentLoaded', () => {

    // Mobile Navigation
    const burger = document.querySelector('.burger');
    const nav = document.querySelector('.nav-links');
    const navLinks = document.querySelectorAll('.nav-links li');

    burger.addEventListener('click', () => {
        // Toggle Nav
        nav.classList.toggle('nav-active');

        // Animate Links
        navLinks.forEach((link, index) => {
            if (link.style.animation) {
                link.style.animation = '';
            } else {
                link.style.animation = `navLinkFade 0.5s ease forwards ${index / 7 + 0.3}s`;
            }
        });

        // Burger Animation
        burger.classList.toggle('toggle');
    });

    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            nav.classList.remove('nav-active'); // Close mobile menu on click
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Scroll Reveal Animation (also includes feed sections)
    const revealElements = document.querySelectorAll('.content-section, .feed-section');

    const revealSection = () => {
        const triggerBottom = window.innerHeight / 5 * 4;

        revealElements.forEach(section => {
            const sectionTop = section.getBoundingClientRect().top;

            if (sectionTop < triggerBottom) {
                section.classList.add('visible');
            } else {
                section.classList.remove('visible');
            }
        });
    }

    window.addEventListener('scroll', revealSection);
    // Trigger once on load
    revealSection();

    // Number Counter Animation
    const counters = document.querySelectorAll('.count');

    const runCounters = () => {
        counters.forEach(counter => {
            if (!counter.getAttribute('data-target')) {
                counter.setAttribute('data-target', '0');
                const final = counter.innerText;
                counter.style.opacity = 0;
                counter.style.transition = "opacity 2s ease";
                counter.innerText = final;
                setTimeout(() => counter.style.opacity = 1, 500);
            }
        });
    }

    runCounters();

    // Parallax Effect for Hero Text
    window.addEventListener('scroll', () => {
        const scrollValue = window.scrollY;
        const heroContent = document.querySelector('.hero-content');
        if (heroContent) {
            heroContent.style.transform = `translateY(${scrollValue * 0.5}px)`;
            heroContent.style.opacity = 1 - (scrollValue / 700);
        }
    });

    // ========================================
    // RSS FEED ENGINE
    // ========================================

    const RSS2JSON_BASE = 'https://api.rss2json.com/v1/api.json?rss_url=';

    // Feed configuration
    const feedConfig = {
        cyber: {
            gridId: 'cyber-feed-grid',
            feeds: [
                { url: 'https://feeds.feedburner.com/TheHackersNews', source: 'The Hacker News', icon: 'fas fa-skull-crossbones' },
                { url: 'https://krebsonsecurity.com/feed/', source: 'Krebs on Security', icon: 'fas fa-user-shield' },
                { url: 'https://www.bleepingcomputer.com/feed/', source: 'BleepingComputer', icon: 'fas fa-bug' },
            ],
            maxItems: 6,
            fallback: [
                { title: "Critical Zero-Day Vulnerability Discovered in Major Enterprise Software", source: "The Hacker News", date: "2026-02-10", link: "https://thehackernews.com", excerpt: "Security researchers have uncovered a critical zero-day vulnerability affecting millions of enterprise systems worldwide." },
                { title: "New Ransomware Strain Targets Healthcare Infrastructure", source: "Krebs on Security", date: "2026-02-09", link: "https://krebsonsecurity.com", excerpt: "A sophisticated new ransomware variant has been observed targeting hospital networks and medical device systems." },
                { title: "State-Sponsored APT Group Launches Supply Chain Attack", source: "BleepingComputer", date: "2026-02-09", link: "https://bleepingcomputer.com", excerpt: "Threat intelligence firms have attributed a massive supply chain compromise to a nation-state advanced persistent threat group." },
                { title: "Browser Extension Vulnerability Exposes 10M Users' Data", source: "The Hacker News", date: "2026-02-08", link: "https://thehackernews.com", excerpt: "A popular browser extension with over 10 million installations was found to be silently exfiltrating user browsing data." },
                { title: "CISA Warns of Active Exploitation of Critical Network Appliance Flaw", source: "BleepingComputer", date: "2026-02-08", link: "https://bleepingcomputer.com", excerpt: "The US cybersecurity agency has added a new critical vulnerability to its Known Exploited Vulnerabilities catalog." },
                { title: "AI-Powered Phishing Campaigns Bypass Traditional Email Filters", source: "Krebs on Security", date: "2026-02-07", link: "https://krebsonsecurity.com", excerpt: "Cybercriminals are leveraging generative AI to craft highly convincing phishing emails that evade security scanning." }
            ]
        },
        travel: {
            gridId: 'travel-feed-grid',
            feeds: [
                { url: 'https://www.nomadicmatt.com/travel-blog/feed/', source: 'Nomadic Matt', icon: 'fas fa-backpack' },
                { url: 'https://www.lonelyplanet.com/feed@v2', source: 'Lonely Planet', icon: 'fas fa-earth-americas' },
                { url: 'https://thepointsguy.com/feed/', source: 'The Points Guy', icon: 'fas fa-credit-card' },
            ],
            maxItems: 6,
            fallback: [
                { title: "The Ultimate Guide to Digital Nomad Visas in 2026", source: "Nomadic Matt", date: "2026-02-10", link: "https://nomadicmatt.com", excerpt: "With more countries offering dedicated digital nomad visas, here's your complete guide to the best options available right now." },
                { title: "Top 10 Coworking Spaces in Southeast Asia for Remote Workers", source: "Lonely Planet", date: "2026-02-09", link: "https://lonelyplanet.com", excerpt: "From Bali to Bangkok, we've scoured the region to find the most inspiring and affordable coworking spaces." },
                { title: "Hidden Gems: 7 European Cities That Won't Break the Bank", source: "The Points Guy", date: "2026-02-09", link: "https://thepointsguy.com", excerpt: "Skip the tourist traps and discover these under-the-radar European destinations perfect for budget-conscious travelers." },
                { title: "Complete Guide to Japan's Rail Pass System for 2026", source: "Nomadic Matt", date: "2026-02-08", link: "https://nomadicmatt.com", excerpt: "Japan has revamped its rail pass options. Here's everything you need to know about the new pricing and routes." },
                { title: "The Rise of Slow Travel: Why Taking Your Time is the New Luxury", source: "Lonely Planet", date: "2026-02-08", link: "https://lonelyplanet.com", excerpt: "More travelers are ditching packed itineraries in favor of deeper, more meaningful connections with their destinations." },
                { title: "Best Travel Credit Card Deals and Sign-Up Bonuses This Month", source: "The Points Guy", date: "2026-02-07", link: "https://thepointsguy.com", excerpt: "We've rounded up the most lucrative travel credit card offers available right now, with sign-up bonuses worth over $1,000." }
            ]
        },
        food: {
            gridId: 'food-feed-grid',
            feeds: [
                { url: 'https://www.seriouseats.com/feeds/atom', source: 'Serious Eats', icon: 'fas fa-utensils' },
                { url: 'https://www.eater.com/rss/index.xml', source: 'Eater', icon: 'fas fa-fire' },
                { url: 'https://food52.com/blog.rss', source: 'Food52', icon: 'fas fa-bowl-food' },
            ],
            maxItems: 6,
            fallback: [
                { title: "The Science Behind the Perfect Bowl of Ramen", source: "Serious Eats", date: "2026-02-10", link: "https://seriouseats.com", excerpt: "From broth chemistry to noodle texture, we break down what makes an unforgettable bowl of ramen on a molecular level." },
                { title: "Street Food Revolution: How Night Markets Are Going Global", source: "Eater", date: "2026-02-09", link: "https://eater.com", excerpt: "Night markets inspired by Asian street food culture are popping up in major cities worldwide, transforming urban food scenes." },
                { title: "10 Essential Spices Every Home Cook Should Stock", source: "Food52", date: "2026-02-09", link: "https://food52.com", excerpt: "Build a spice collection that will elevate your cooking from everyday meals to restaurant-quality dishes." },
                { title: "The Best Food Cities for Digital Nomads on a Budget", source: "Eater", date: "2026-02-08", link: "https://eater.com", excerpt: "Where incredible cuisine meets affordable living — our guide to eating like royalty without the price tag." },
                { title: "Fermentation 101: A Beginner's Guide to Making Your Own Kimchi", source: "Serious Eats", date: "2026-02-08", link: "https://seriouseats.com", excerpt: "Master the ancient art of fermentation with our step-by-step guide to crafting perfect homemade kimchi." },
                { title: "Why Everyone Is Obsessed With Japanese Convenience Store Food", source: "Food52", date: "2026-02-07", link: "https://food52.com", excerpt: "Onigiri, egg sandwiches, and more — exploring why konbini food has become a global culinary phenomenon." }
            ]
        }
    };

    // Helper: Format date string
    function formatDate(dateStr) {
        try {
            const d = new Date(dateStr);
            if (isNaN(d.getTime())) return 'Recent';
            const now = new Date();
            const diffMs = now - d;
            const diffHrs = Math.floor(diffMs / (1000 * 60 * 60));
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

            if (diffHrs < 1) return 'Just now';
            if (diffHrs < 24) return `${diffHrs}h ago`;
            if (diffDays < 7) return `${diffDays}d ago`;
            return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
        } catch {
            return 'Recent';
        }
    }

    // Helper: Strip HTML tags from text
    function stripHtml(html) {
        const temp = document.createElement('div');
        temp.innerHTML = html;
        return temp.textContent || temp.innerText || '';
    }

    // Helper: Create a feed card element
    function createFeedCard(item) {
        const card = document.createElement('a');
        card.className = 'feed-card';
        card.href = item.link;
        card.target = '_blank';
        card.rel = 'noopener noreferrer';

        const excerpt = item.excerpt || item.description || '';
        const cleanExcerpt = stripHtml(excerpt).substring(0, 150);

        card.innerHTML = `
            <div class="card-source">
                <i class="${item.icon || 'fas fa-rss'}"></i>
                ${item.source}
            </div>
            <div class="card-title">${stripHtml(item.title)}</div>
            <div class="card-excerpt">${cleanExcerpt}${cleanExcerpt.length >= 150 ? '...' : ''}</div>
            <div class="card-meta">
                <span class="card-date">${formatDate(item.date || item.pubDate)}</span>
                <span class="card-link">Read <i class="fas fa-arrow-right"></i></span>
            </div>
        `;

        return card;
    }

    // Fetch a single RSS feed and return parsed items
    async function fetchRSSFeed(feedUrl, sourceName, sourceIcon) {
        try {
            const response = await fetch(`${RSS2JSON_BASE}${encodeURIComponent(feedUrl)}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            if (data.status !== 'ok' || !data.items) throw new Error('Invalid feed data');

            return data.items.map(item => ({
                title: item.title,
                description: item.description || item.content || '',
                link: item.link,
                date: item.pubDate,
                source: sourceName,
                icon: sourceIcon
            }));
        } catch (err) {
            console.warn(`Feed fetch failed for ${sourceName}: ${err.message}`);
            return null;
        }
    }

    // Load feeds for a category
    async function loadFeedCategory(config) {
        const grid = document.getElementById(config.gridId);
        if (!grid) return [];

        let allItems = [];
        let feedsFailed = 0;

        // Try to fetch all feeds in parallel
        const feedPromises = config.feeds.map(f => fetchRSSFeed(f.url, f.source, f.icon));
        const results = await Promise.allSettled(feedPromises);

        results.forEach((result, idx) => {
            if (result.status === 'fulfilled' && result.value) {
                allItems = allItems.concat(result.value);
            } else {
                feedsFailed++;
            }
        });

        // If all feeds failed, use fallback data
        if (allItems.length === 0) {
            console.log(`All feeds failed for ${config.gridId}, using fallback data`);
            allItems = config.fallback.map(item => ({
                ...item,
                icon: config.feeds[0]?.icon || 'fas fa-rss',
                description: item.excerpt
            }));
        }

        // Sort by date (newest first)
        allItems.sort((a, b) => {
            const dateA = new Date(a.date || a.pubDate || 0);
            const dateB = new Date(b.date || b.pubDate || 0);
            return dateB - dateA;
        });

        // Limit to maxItems
        const displayItems = allItems.slice(0, config.maxItems);

        // Clear skeleton loaders
        grid.innerHTML = '';

        // Render cards with staggered animation
        displayItems.forEach((item, index) => {
            const card = createFeedCard(item);
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            grid.appendChild(card);

            // Stagger the entrance animation
            setTimeout(() => {
                card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Show error state if nothing rendered
        if (displayItems.length === 0) {
            grid.innerHTML = `
                <div class="feed-error">
                    <i class="fas fa-satellite-dish"></i>
                    <p>Signal lost. Feeds temporarily unavailable.</p>
                </div>
            `;
        }

        return displayItems;
    }

    // ========================================
    // IDENTITY PULSE ENGINE
    // ========================================

    function updateIdentityPulse(cyberItems) {
        // 1. Trend Analysis
        const protocolKeywords = ['OAuth', 'OIDC', 'SAML', 'FIDO2', 'WebAuthn', 'Zero Trust', 'Passkeys', 'MFA', 'JWT', 'Entra', 'Okta', 'Ransomware', 'AI'];
        const counts = {};

        if (cyberItems && cyberItems.length > 0) {
            cyberItems.forEach(item => {
                const text = (item.title + ' ' + item.description).toLowerCase();
                protocolKeywords.forEach(keyword => {
                    if (text.includes(keyword.toLowerCase())) {
                        counts[keyword] = (counts[keyword] || 0) + 1;
                    }
                });
            });
        }

        // Find max
        let max = 0;
        let trend = 'Zero Trust'; // Default
        for (const [key, value] of Object.entries(counts)) {
            if (value > max) {
                max = value;
                trend = key;
            }
        }

        const trendElement = document.getElementById('pulse-protocol');
        if (trendElement) {
            trendElement.innerText = trend;
            trendElement.style.opacity = 0;
            setTimeout(() => {
                trendElement.style.opacity = 1;
                // Add a blink effect
                trendElement.style.transition = 'opacity 0.5s';
            }, 100);
        }

        // 2. Active CVE Counter (Simulated Live Data)
        const cveElement = document.getElementById('pulse-cve');
        if (cveElement) {
            let count = 342; // Starting baseline
            cveElement.innerText = count;

            // Randomly update every few seconds to look "live"
            setInterval(() => {
                if (Math.random() > 0.6) {
                    const change = Math.floor(Math.random() * 5) - 2; // -2 to +2
                    count += change;
                    cveElement.innerText = count;
                    cveElement.style.color = '#fff';
                    setTimeout(() => cveElement.style.color = '#ff4d4d', 200);
                }
            }, 3000);
        }
    }

    // ========================================
    // MODAL SYSTEM (Quick Tools)
    // ========================================

    // Cheat Sheet Content
    const cheatSheets = {
        'oidc-cheat': {
            title: 'OIDC Scopes Cheat Sheet',
            content: `
                <div style="text-align: left;">
                    <div style="margin-bottom: 10px;"><strong>openid</strong>: Required. Signals OIDC request.</div>
                    <div style="margin-bottom: 10px;"><strong>profile</strong>: Returns name, family_name, given_name, etc.</div>
                    <div style="margin-bottom: 10px;"><strong>email</strong>: Returns email, email_verified.</div>
                    <div style="margin-bottom: 10px;"><strong>address</strong>: Returns address info.</div>
                    <div style="margin-bottom: 10px;"><strong>phone</strong>: Returns phone_number.</div>
                    <div style="margin-bottom: 10px;"><strong>offline_access</strong>: Returns Refresh Token.</div>
                </div>
            `
        },
        'saml-cheat': {
            title: 'SAML Assertion Structure',
            content: `
                <div style="text-align: left; font-family: monospace; font-size: 0.8rem; background: rgba(0,0,0,0.3); padding: 10px; border-radius: 5px;">
                    &lt;saml:Assertion&gt;<br>
                    &nbsp;&nbsp;&lt;saml:Issuer&gt;https://idp.com&lt;/saml:Issuer&gt;<br>
                    &nbsp;&nbsp;&lt;saml:Subject&gt;<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&lt;saml:NameID&gt;user@example.com&lt;/saml:NameID&gt;<br>
                    &nbsp;&nbsp;&lt;/saml:Subject&gt;<br>
                    &nbsp;&nbsp;&lt;saml:AttributeStatement&gt;<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&lt;saml:Attribute Name="Role"&gt;...&lt;/saml:Attribute&gt;<br>
                    &nbsp;&nbsp;&lt;/saml:AttributeStatement&gt;<br>
                    &lt;/saml:Assertion&gt;
                </div>
            `
        }
    };

    window.openModal = function (id) {
        const data = cheatSheets[id];
        if (!data) return;

        // Remove existing modal if any
        const existing = document.querySelector('.custom-modal-overlay');
        if (existing) existing.remove();

        const modal = document.createElement('div');
        modal.className = 'custom-modal-overlay';
        modal.style.cssText = `
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.8); backdrop-filter: blur(5px);
            z-index: 1000; display: flex; justify-content: center; align-items: center;
            opacity: 0; transition: opacity 0.3s;
        `;

        modal.innerHTML = `
            <div class="custom-modal" style="
                background: #1a1a2e; border: 1px solid var(--neon-cyan);
                padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%;
                position: relative; box-shadow: 0 0 30px rgba(0, 243, 255, 0.2);
                transform: scale(0.9); transition: transform 0.3s;
            ">
                <button onclick="this.closest('.custom-modal-overlay').remove()" style="
                    position: absolute; top: 10px; right: 10px; background: none; border: none;
                    color: #fff; font-size: 1.5rem; cursor: pointer;
                ">&times;</button>
                <h3 style="color: var(--neon-cyan); margin-bottom: 1.5rem; font-family: 'Space Mono', monospace;">${data.title}</h3>
                <div style="color: #ccc; line-height: 1.6;">${data.content}</div>
            </div>
        `;

        document.body.appendChild(modal);

        // Animate in
        setTimeout(() => {
            modal.style.opacity = '1';
            modal.querySelector('.custom-modal').style.transform = 'scale(1)';
        }, 10);

        // Close on click outside
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.remove();
        });
    };

    // Initialize all feeds
    async function initFeeds() {
        // Small delay so the page loads the visual elements first
        await new Promise(resolve => setTimeout(resolve, 500));

        // Load all feed categories in parallel
        // We capture cyberItems specifically for analysis
        const [cyberItems] = await Promise.all([
            loadFeedCategory(feedConfig.cyber),
            loadFeedCategory(feedConfig.travel),
            loadFeedCategory(feedConfig.food)
        ]);

        // Run Identity Pulse analysis
        updateIdentityPulse(cyberItems);
    }

    // Fire up the feeds
    initFeeds();

    // Check for status messages (for Index page contact form)
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const indexStatusDiv = document.getElementById('index-form-status');

    if (indexStatusDiv && status) {
        if (status === 'success') {
            indexStatusDiv.innerText = "Message sent successfully! 🚀";
            indexStatusDiv.style.display = 'block';
            indexStatusDiv.style.color = '#00f3ff'; // Neon Cyan
        } else if (status === 'error') {
            indexStatusDiv.innerText = "Failed to send message. Please try again. ⚠️";
            indexStatusDiv.style.display = 'block';
            indexStatusDiv.style.color = '#ff4d4d';
        }
    }

    // ========================================
    // THREAT DASHBOARD ENGINE (v2.4 - TDZ Fix)
    // ========================================

    // NOTE: initDashboard() is called at the BOTTOM of this section,
    // AFTER all let/const variable declarations, to avoid Temporal Dead Zone errors.

    function initDashboard() {
        console.log("[Dashboard] Starting initialization...");
        updateTime();
        initGauge();
        initIndustryInsights();
        initAlertsFeed();

        // Update every minute (gauge and time)
        setInterval(() => {
            updateTime();
            updateGauge(Math.floor(Math.random() * 10) - 5);
        }, 60000);

        // Map Tab Switcher Logic
        const tabBtns = document.querySelectorAll('.map-tab-btn');
        const mapViews = document.querySelectorAll('.map-view');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const mapId = btn.getAttribute('data-map');

                // Update buttons
                tabBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                // Update views
                mapViews.forEach(view => {
                    view.classList.remove('active');
                    if (view.id === `map-${mapId}`) {
                        view.classList.add('active');
                    }
                });

                console.log(`[Dashboard] Switched to ${mapId} map`);
            });
        });

        // Refresh alerts & insights every 5 minutes
        setInterval(() => {
            console.log("[Dashboard] Auto-refreshing feeds...");
            initAlertsFeed();
            initIndustryInsights();
        }, 300000);

        console.log("[Dashboard] Initialization complete. Feeds will auto-refresh every 5 minutes.");
    }

    function updateTime() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const el = document.getElementById('last-updated');
        if (el) el.innerText = `Updated: ${timeString}`;
    }

    // --- Threat Gauge ---
    let currentThreatLevel = 75; // Initial Baseline

    function initGauge() {
        const hour = new Date().getHours();
        const hourlySeed = (hour * 9301 + 49297) % 100;
        currentThreatLevel = 40 + (hourlySeed / 2);
        renderGauge(currentThreatLevel);
        console.log("[Dashboard] Gauge initialized at", Math.round(currentThreatLevel));
    }

    function updateGauge(change) {
        currentThreatLevel += change;
        if (currentThreatLevel > 100) currentThreatLevel = 100;
        if (currentThreatLevel < 0) currentThreatLevel = 0;
        renderGauge(currentThreatLevel);
    }

    function renderGauge(score) {
        const fill = document.getElementById('threat-gauge-fill');
        const text = document.getElementById('threat-text');
        const scoreEl = document.getElementById('threat-score');
        const alertBanner = document.getElementById('gauge-alert-banner');

        if (!fill) return;

        const rotation = -45 + (score / 100) * 270;
        fill.style.transform = `rotate(${rotation}deg)`;

        let levelText = "Low";
        let color = "#00ff00";

        if (score > 30) { levelText = "Moderate"; color = "#ffd700"; }
        if (score > 60) { levelText = "Elevated"; color = "#ff8c00"; }
        if (score > 80) { levelText = "Critical"; color = "#ff4d4d"; }

        text.innerText = levelText;
        text.style.color = color;
        scoreEl.innerText = `${Math.round(score)} / 100`;
        fill.style.borderTopColor = color;
        fill.style.borderRightColor = color;

        if (alertBanner) {
            if (score > 60) {
                alertBanner.innerHTML = `<span class="blink">●</span> Action required for high-risk identities`;
                alertBanner.style.background = `rgba(${score > 80 ? '255, 77, 77' : '255, 140, 0'}, 0.2)`;
                alertBanner.style.color = color;
            } else {
                alertBanner.innerHTML = `<i class="fas fa-check-circle"></i> Systems Nominal`;
                alertBanner.style.background = "rgba(0, 255, 0, 0.1)";
                alertBanner.style.color = "#00ff00";
            }
        }
    }

    // --- Industry Insights ---
    const sectorData = {
        all: [
            { name: "Finance", value: 85 },
            { name: "Retail", value: 65 },
            { name: "Tech", value: 72 },
            { name: "Healthcare", value: 58 },
            { name: "Industrial", value: 45 }
        ],
        finance: [
            { name: "Banking", value: 92 },
            { name: "Insurance", value: 78 },
            { name: "FinTech", value: 88 },
            { name: "Crypto", value: 82 }
        ],
        retail: [
            { name: "E-comm", value: 75 },
            { name: "POS", value: 62 },
            { name: "Supply", value: 55 },
            { name: "Loyalty", value: 48 }
        ],
        industrial: [
            { name: "Energy", value: 88 },
            { name: "Manuf.", value: 65 },
            { name: "Logistics", value: 70 },
            { name: "IoT", value: 82 }
        ],
        banking: [
            { name: "Retail", value: 85 },
            { name: "Invest.", value: 95 },
            { name: "SWIFT", value: 78 },
            { name: "ATM", value: 60 }
        ]
    };

    function initIndustryInsights() {
        const container = document.getElementById('sector-bars');
        const buttons = document.querySelectorAll('.filter-btn');

        if (!container) return;

        const renderBars = (category) => {
            const data = sectorData[category] || sectorData.all;
            container.innerHTML = '';

            data.forEach((item, index) => {
                const bar = document.createElement('div');
                bar.className = 'sector-bar';
                bar.innerHTML = `
                    <div class="sector-name">${item.name}</div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: 0%"></div>
                    </div>
                    <div class="sector-value">${item.value}%</div>
                `;
                container.appendChild(bar);

                setTimeout(() => {
                    bar.querySelector('.bar-fill').style.width = `${item.value}%`;
                }, index * 100);
            });
        };

        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const filter = btn.getAttribute('data-filter');
                renderBars(filter);
            });
        });
        renderBars('all');
        console.log("[Dashboard] Industry insights initialized");
    }

    // --- Alerts Feed (LIVE DATA with robust fallback) ---
    const alertFeeds = [
        { url: 'https://www.bleepingcomputer.com/feed/', source: 'BleepingComputer', icon: 'fas fa-bug' },
        { url: 'https://feeds.feedburner.com/TheHackersNews', source: 'Hacker News', icon: 'fas fa-user-secret' },
        { url: 'https://krebsonsecurity.com/feed/', source: 'Krebs on Security', icon: 'fas fa-user-shield' }
    ];

    // Dashboard-specific RSS fetch (independent of homepage fetchRSSFeed)
    async function fetchDashboardFeed(feedUrl, sourceName, sourceIcon) {
        try {
            const response = await fetch(`${RSS2JSON_BASE}${encodeURIComponent(feedUrl)}`);
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const data = await response.json();
            if (data.status !== 'ok' || !data.items) throw new Error('Invalid feed data');

            return data.items.map(item => ({
                title: item.title || 'Untitled',
                description: item.description || item.content || '',
                link: item.link || '#',
                date: item.pubDate || new Date().toISOString(),
                source: sourceName,
                icon: sourceIcon
            }));
        } catch (err) {
            console.warn(`[Dashboard] Feed fetch failed for ${sourceName}: ${err.message}`);
            return null;
        }
    }

    // Fallback data — always available
    const fallbackAlerts = [
        {
            title: "Critical Zero-Day Vulnerability Discovered in Major Enterprise Software",
            source: "The Hacker News",
            date: new Date().toISOString(),
            description: "Security researchers have uncovered a critical zero-day vulnerability affecting millions of enterprise systems worldwide.",
            icon: "fas fa-skull-crossbones",
            link: "https://thehackernews.com"
        },
        {
            title: "New Ransomware Strain Targets Healthcare Infrastructure",
            source: "Krebs on Security",
            date: new Date(Date.now() - 3600000 * 4).toISOString(),
            description: "A sophisticated new ransomware variant has been observed targeting hospital networks and medical devices.",
            icon: "fas fa-user-shield",
            link: "https://krebsonsecurity.com"
        },
        {
            title: "State-Sponsored APT Group Launches Supply Chain Attack",
            source: "BleepingComputer",
            date: new Date(Date.now() - 3600000 * 12).toISOString(),
            description: "Threat intelligence firms have attributed a massive supply chain compromise to a nation-state group.",
            icon: "fas fa-bug",
            link: "https://bleepingcomputer.com"
        },
        {
            title: "Browser Extension Vulnerability Exposes 10M Users' Data",
            source: "The Hacker News",
            date: new Date(Date.now() - 3600000 * 24).toISOString(),
            description: "A popular browser extension was found to be silently exfiltrating user browsing data to external servers.",
            icon: "fas fa-user-secret",
            link: "https://thehackernews.com"
        },
        {
            title: "CISA Warns of Active Exploitation of Network Appliance Flaw",
            source: "BleepingComputer",
            date: new Date(Date.now() - 3600000 * 26).toISOString(),
            description: "The US cybersecurity agency has added a new critical vulnerability to its Known Exploited Vulnerabilities catalog.",
            icon: "fas fa-flag-usa",
            link: "https://bleepingcomputer.com"
        },
        {
            title: "Entra ID Conditional Access Bypass Technique Disclosed",
            source: "The Hacker News",
            date: new Date(Date.now() - 3600000 * 36).toISOString(),
            description: "Researchers demonstrate a method to bypass Azure Entra ID conditional access policies using legacy authentication.",
            icon: "fas fa-skull-crossbones",
            link: "https://thehackernews.com"
        },
        {
            title: "Massive Credential Stuffing Campaign Targets Identity Providers",
            source: "Krebs on Security",
            date: new Date(Date.now() - 3600000 * 48).toISOString(),
            description: "A coordinated credential stuffing attack has targeted multiple SSO and identity providers affecting thousands of organizations.",
            icon: "fas fa-user-shield",
            link: "https://krebsonsecurity.com"
        }
    ];

    async function initAlertsFeed() {
        const feed = document.getElementById('alerts-feed');
        if (!feed) {
            console.warn("[Dashboard] #alerts-feed container not found");
            return;
        }

        console.log("[Dashboard] Initializing alerts feed...");

        // Show loading state
        feed.innerHTML = `
            <div class="alert-skeleton"></div>
            <div class="alert-skeleton"></div>
            <div class="alert-skeleton"></div>
        `;

        let liveAlerts = [];

        try {
            // Timeout: 6 seconds — gives RSS2JSON API enough time  
            const timeout = new Promise((_, reject) =>
                setTimeout(() => reject(new Error('Feed timeout after 6s')), 6000)
            );

            const fetchPromise = (async () => {
                const promises = alertFeeds.map(f => fetchDashboardFeed(f.url, f.source, f.icon));
                const results = await Promise.allSettled(promises);
                let items = [];
                results.forEach(res => {
                    if (res.status === 'fulfilled' && res.value) {
                        items = items.concat(res.value);
                    }
                });
                console.log(`[Dashboard] Fetched ${items.length} live items`);
                return items;
            })();

            // Race!
            const fetchedItems = await Promise.race([fetchPromise, timeout]);

            if (fetchedItems && fetchedItems.length > 0) {
                liveAlerts = fetchedItems;
                console.log("[Dashboard] Using LIVE feed data");
            } else {
                throw new Error("No items returned from any feed");
            }

            // Sort by date (newest first)
            liveAlerts.sort((a, b) => new Date(b.date) - new Date(a.date));
            liveAlerts = liveAlerts.slice(0, 10);

        } catch (e) {
            console.warn("[Dashboard] Live feed issue, using fallback:", e.message);
            liveAlerts = fallbackAlerts;
            console.log("[Dashboard] Using FALLBACK data (" + liveAlerts.length + " items)");
        }

        // Render the alerts
        console.log("[Dashboard] Rendering " + liveAlerts.length + " alerts...");
        feed.innerHTML = '';

        liveAlerts.forEach((alert, index) => {
            renderAlertItem(feed, alert, index);
        });

        console.log("[Dashboard] Alerts feed rendering complete");
    }

    function renderAlertItem(container, alert, index) {
        // Determine severity based on keywords
        let severity = 'medium';
        const titleText = (alert.title || '').toLowerCase();
        const descText = (alert.description || '').toLowerCase();
        const combinedText = titleText + ' ' + descText;

        if (combinedText.includes('critical') || combinedText.includes('zero-day') || combinedText.includes('0-day') || combinedText.includes('ransomware')) {
            severity = 'critical';
        } else if (combinedText.includes('high') || combinedText.includes('exploit') || combinedText.includes('vuln') || combinedText.includes('bypass')) {
            severity = 'high';
        }

        const item = document.createElement('div');
        item.className = 'alert-item';
        // Set initial hidden state WITH transition already defined
        item.style.cssText = 'opacity: 0; transform: translateX(-20px); transition: opacity 0.4s ease, transform 0.4s ease;';

        // Format relative time — handle both Date objects and strings
        let timeStr = "Just now";
        try {
            if (alert.date) {
                const dateObj = (alert.date instanceof Date) ? alert.date : new Date(alert.date);
                if (!isNaN(dateObj.getTime())) {
                    timeStr = formatDate(dateObj);
                }
            }
        } catch (e) {
            timeStr = "Recent";
        }

        item.innerHTML = `
            <div class="alert-icon" style="background: rgba(255,255,255,0.05); color: #fff;">
                <i class="${alert.icon || 'fas fa-shield-alt'}"></i>
            </div>
            <div class="alert-content">
                <div class="alert-header">
                    <span class="alert-source">${alert.source || 'Unknown'}</span>
                    <span class="alert-severity ${severity}">${severity}</span>
                </div>
                <div class="alert-title">${alert.title || 'Untitled Alert'}</div>
                <div class="alert-meta">
                    <span>${timeStr}</span>
                    <a href="${alert.link || '#'}" target="_blank" rel="noopener" class="investigate-btn">View Intel <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        `;

        container.appendChild(item);

        // Animate in with staggered delay
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 200 + (index * 150));
    }

    // === TRIGGER: Initialize dashboard AFTER all declarations ===
    if (document.querySelector('.dashboard-container')) {
        console.log("Dashboard container found, initializing Threat Dashboard...");
        initDashboard();
    }

    // ========================================
    // SIDEBAR LEADERBOARD (Home Page)
    // ========================================

    async function updateSidebarLeaderboard() {
        const sidebar = document.getElementById('sidebar-leaderboard');
        if (!sidebar) return;

        try {
            const resp = await fetch('academy.php?action=get_leaderboard');
            if (!resp.ok) throw new Error("Leaderboard source unavailable");
            const data = await resp.json();

            if (data.length === 0) {
                sidebar.innerHTML = '<div style="color: var(--text-muted); font-size: 0.8rem;">No active operators.</div>';
                return;
            }

            // Take top 5
            const top5 = data.slice(0, 5);
            sidebar.innerHTML = '';

            top5.forEach((user, idx) => {
                const item = document.createElement('div');
                item.style.cssText = `
                    display: flex; justify-content: space-between; align-items: center;
                    padding: 0.8rem 0; border-bottom: 1px solid rgba(255,255,255,0.05);
                    font-family: 'Space Mono', monospace;
                `;

                item.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 0.8rem;">
                        <span style="color: ${idx === 0 ? 'var(--neon-gold)' : 'var(--text-muted)'}; font-weight: 800;">#${idx + 1}</span>
                        <span style="color: #fff; font-size: 0.9rem;">${user.name}</span>
                    </div>
                    <div style="text-align: right;">
                        <div style="color: var(--neon-cyan); font-size: 0.8rem;">${user.score}/25</div>
                    </div>
                `;
                sidebar.appendChild(item);
            });

        } catch (e) {
            console.warn("[Leaderboard] Failed to load sidebar:", e);
            sidebar.innerHTML = '<div style="color: var(--text-muted); font-size: 0.8rem;">Offline Mode.</div>';
        }
    }

    if (document.getElementById('sidebar-leaderboard')) {
        updateSidebarLeaderboard();
    }

});
