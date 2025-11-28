<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taxly Ecosystem - Security Architecture</title>
    <meta name="description"
        content="Comprehensive security architecture for Taxly E-Invoicing ecosystem with FIRS compliance.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .security-card {
            background: #1e293b;
            border: 1px solid #334155;
        }

        .text-slate-300 {
            color: #cbd5e1;
        }

        .text-slate-400 {
            color: #94a3b8;
        }

        .text-blue-400 {
            color: #60a5fa;
        }

        .text-green-400 {
            color: #4ade80;
        }

        .text-purple-400 {
            color: #c084fc;
        }

        .text-red-400 {
            color: #f87171;
        }
    </style>
</head>

<body class="bg-slate-900 text-white">
    <!-- Navigation -->
    <nav class="bg-slate-900 border-b border-slate-700 p-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold gradient-text">Taxly Ecosystem</h1>
            <div class="space-x-4">
                <a href="/" class="text-slate-300 hover:text-blue-400">Home</a>
                <a href="#architecture" class="text-slate-300 hover:text-blue-400">Architecture</a>
                <a href="#security" class="text-slate-300 hover:text-blue-400">Security</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg py-20 text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-5xl font-bold text-white mb-6">
                Taxly E-Invoicing<br>
                <span class="text-blue-400">Security Ecosystem</span>
            </h1>
            <p class="text-xl text-slate-300 mb-8">
                Comprehensive security architecture for FIRS compliance. Military-grade protection powering B2B and B2C
                electronic invoicing across Nigeria.
            </p>
            <div class="flex justify-center space-x-4">
                <a href="#architecture"
                    class="bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Explore Architecture
                </a>
                <a href="#security"
                    class="border border-blue-400 text-blue-400 px-6 py-3 rounded-lg font-semibold hover:bg-blue-400 hover:text-white transition-colors">
                    View Security
                </a>
            </div>
        </div>
    </section>

    <!-- Architecture Overview -->
    <section id="architecture" class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12">🔒 Security Architecture</h2>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Business Applications -->
                <div class="security-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-building text-blue-400 mr-3"></i>
                        Business Applications
                    </h3>

                    <div class="space-y-4">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h4 class="font-semibold text-blue-400">✓ Vendra</h4>
                            <p class="text-slate-300 text-sm">End-to-end vendor lifecycle platform with streamlined KYC
                                onboarding and bidirectional invoice flow.</p>
                        </div>

                        <div class="border-l-4 border-purple-500 pl-4">
                            <h4 class="font-semibold text-purple-400">△ Akraa</h4>
                            <p class="text-slate-300 text-sm">Specialized invoicing solution with industry-tailored
                                workflows.</p>
                        </div>

                        <div class="border-l-4 border-green-500 pl-4">
                            <h4 class="font-semibold text-green-400">🏪 ATRS Fiscalization</h4>
                            <p class="text-slate-300 text-sm">B2C fiscalization platform with hotel PMS integration and
                                real-time receipt generation.</p>
                        </div>
                    </div>
                </div>

                <!-- Vendra Components -->
                <div class="security-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-cogs text-indigo-400 mr-3"></i>
                        Vendra Components
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <h4 class="font-semibold text-indigo-400">✅ Onboarding Engine</h4>
                            <p class="text-slate-300 text-sm">Automated vendor verification with identity validation and
                                FIRS verification.</p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-indigo-400">📄 PO Orchestration</h4>
                            <p class="text-slate-300 text-sm">Purchase order lifecycle with FIRS-compliant formatting.
                            </p>
                        </div>

                        <div>
                            <h4 class="font-semibold text-indigo-400">📨 Invoice Routing</h4>
                            <p class="text-slate-300 text-sm">Intelligent invoice reception with three-way matching.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Perimeter -->
    <section id="security" class="py-16 px-4 bg-slate-800">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12">🛡️ Security Perimeter</h2>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="security-card p-6 rounded-xl text-center">
                    <i class="fas fa-fire text-red-400 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">🔥 Threat Protection</h3>
                    <p class="text-slate-300 text-sm">ML-powered WAF, DDoS shield, and intelligent bot filtering.</p>
                </div>

                <div class="security-card p-6 rounded-xl text-center">
                    <i class="fas fa-door-open text-blue-400 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">🚪 API Gateway</h3>
                    <p class="text-slate-300 text-sm">Token validation, rate limiting, and zero-trust verification.</p>
                </div>

                <div class="security-card p-6 rounded-xl text-center">
                    <i class="fas fa-balance-scale text-green-400 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">⚖️ Load Distribution</h3>
                    <p class="text-slate-300 text-sm">Multi-zone deployment with health-aware routing.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Taxly Core Platform -->
    <section class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12">⚙️ Taxly Core Platform</h2>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="security-card p-6 rounded-xl text-center">
                    <i class="fas fa-rocket text-green-400 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">🚀 Compliance Engine</h3>
                    <p class="text-slate-300 text-sm">FIRS-certified infrastructure with complete audit trails.</p>
                </div>

                <div class="security-card p-6 rounded-xl text-center">
                    <i class="fas fa-signature text-purple-400 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">✍️ Cryptographic Signing</h3>
                    <p class="text-slate-300 text-sm">FIPS 140-2 Level 3 HSM with RSA-2048 signatures.</p>
                </div>

                <div class="security-card p-6 rounded-xl text-center">
                    <i class="fas fa-qrcode text-blue-400 text-3xl mb-4"></i>
                    <h3 class="text-xl font-bold mb-3">📱 Verification Codes</h3>
                    <p class="text-slate-300 text-sm">Tamper-evident QR codes with public verification.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Government Integration -->
    <section class="py-16 px-4 bg-slate-800">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12">🏛️ Government Integration</h2>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="security-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-university text-green-400 mr-3"></i>
                        🏛️ FIRS Connection
                    </h3>
                    <p class="text-slate-300 mb-4">Dedicated government gateway with private network tunnel and mutual
                        certificate authentication.</p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-lock text-green-400 mr-3"></i>
                            <span class="text-sm">Private Tunnel</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-certificate text-green-400 mr-3"></i>
                            <span class="text-sm">Mutual TLS</span>
                        </div>
                    </div>
                </div>

                <div class="security-card p-6 rounded-xl">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-sync-alt text-blue-400 mr-3"></i>
                        🔄 Resilience Layer
                    </h3>
                    <p class="text-slate-300 mb-4">Fault-tolerant submission with intelligent retry and automated
                        reconciliation.</p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-redo text-blue-400 mr-3"></i>
                            <span class="text-sm">Smart Retry</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-check-double text-blue-400 mr-3"></i>
                            <span class="text-sm">Auto Reconcile</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Controls -->
    <section class="py-16 px-4">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-3xl font-bold text-center mb-12">🔐 Security Controls</h2>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="security-card p-4 rounded-xl text-center">
                    <i class="fas fa-lock text-blue-400 text-2xl mb-3"></i>
                    <h4 class="font-semibold text-blue-400">Transport Security</h4>
                    <p class="text-slate-400 text-xs">TLS 1.3 with forward secrecy</p>
                </div>

                <div class="security-card p-4 rounded-xl text-center">
                    <i class="fas fa-key text-purple-400 text-2xl mb-3"></i>
                    <h4 class="font-semibold text-purple-400">Cryptography</h4>
                    <p class="text-slate-400 text-xs">AES-256, RSA-2048, SHA-256</p>
                </div>

                <div class="security-card p-4 rounded-xl text-center">
                    <i class="fas fa-fingerprint text-green-400 text-2xl mb-3"></i>
                    <h4 class="font-semibold text-green-400">Identity</h4>
                    <p class="text-slate-400 text-xs">OAuth 2.0, MFA, SMS OTP</p>
                </div>

                <div class="security-card p-4 rounded-xl text-center">
                    <i class="fas fa-clipboard-check text-orange-400 text-2xl mb-3"></i>
                    <h4 class="font-semibold text-orange-400">Compliance</h4>
                    <p class="text-slate-400 text-xs">Hash-chained logs, WORM archive</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 py-8 px-4 border-t border-slate-700">
        <div class="max-w-6xl mx-auto text-center">
            <p class="text-slate-400">&copy; 2024 Taxly. All rights reserved. Military-grade security for Nigerian
                e-invoicing.</p>
            <div class="mt-4">
                <a href="/" class="text-blue-400 hover:text-blue-300">← Back to Home</a>
            </div>
        </div>
    </footer>
</body>

</html>
