<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taxly Ecosystem - Detailed Security Architecture</title>
    <meta name="description"
        content="Comprehensive security architecture for Taxly E-Invoicing ecosystem with FIRS compliance.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

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
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .threat-card {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(249, 115, 22, 0.1) 100%);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .compliance-card {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(22, 163, 74, 0.1) 100%);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .flow-step {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .code-block {
            background: #1e293b;
            color: #e2e8f0;
            border: 1px solid #334155;
        }

        .interactive-hover {
            transition: all 0.3s ease;
        }

        .interactive-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        /* Ensure all text is visible */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        div,
        li,
        td,
        th {
            color: #f8fafc;
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

        .text-orange-400 {
            color: #fb923c;
        }

        .text-yellow-400 {
            color: #facc15;
        }

        .text-teal-400 {
            color: #2dd4bf;
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1e293b;
        }

        ::-webkit-scrollbar-thumb {
            background: #3b82f6;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #60a5fa;
        }
    </style>
</head>

<body class="bg-slate-900 text-white">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-slate-900/95 backdrop-blur-md z-50 border-b border-slate-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('logo.png') }}" alt="no logo" class="h-10 w-auto">
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="/"
                            class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Home</a>
                        <a href="#overview"
                            class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Overview</a>
                        <a href="#applications"
                            class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Applications</a>
                        <a href="#security"
                            class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Security</a>
                        <a href="#flow"
                            class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Security
                            Flow</a>
                        <a href="#controls"
                            class="text-slate-300 hover:text-blue-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Controls</a>
                    </div>
                </div>
                <div class="md:hidden">
                    <button type="button"
                        class="text-slate-300 hover:text-blue-400 focus:outline-none focus:text-blue-400"
                        id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="hidden md:hidden bg-slate-800 border-t border-slate-700" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="/" class="block px-3 py-2 text-slate-300 hover:text-blue-400">Home</a>
                <a href="#overview" class="block px-3 py-2 text-slate-300 hover:text-blue-400">Overview</a>
                <a href="#applications" class="block px-3 py-2 text-slate-300 hover:text-blue-400">Applications</a>
                <a href="#security" class="block px-3 py-2 text-slate-300 hover:text-blue-400">Security</a>
                <a href="#flow" class="block px-3 py-2 text-slate-300 hover:text-blue-400">Security Flow</a>
                <a href="#controls" class="block px-3 py-2 text-slate-300 hover:text-blue-400">Controls</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black/40"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center" data-aos="fade-up">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Taxly E-Invoicing<br>
                <span class="text-blue-400">Security Ecosystem</span>
            </h1>
            <p class="text-xl text-slate-300 mb-8 max-w-4xl mx-auto">
                Comprehensive security architecture for FIRS compliance. Military-grade protection powering B2B and B2C
                electronic invoicing across Nigeria.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#overview"
                    class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                    Explore Architecture
                </a>
                <a href="#flow"
                    class="border-2 border-blue-400 text-blue-400 px-8 py-3 rounded-lg font-semibold hover:bg-blue-400 hover:text-white transition-colors">
                    View Security Flow
                </a>
            </div>
        </div>

        <!-- Floating security icons -->
        <div
            class="absolute top-20 left-10 w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center animate-pulse">
            <i class="fas fa-shield-alt text-blue-400 text-2xl"></i>
        </div>
        <div
            class="absolute bottom-20 right-10 w-20 h-20 bg-green-500/20 rounded-full flex items-center justify-center animate-pulse delay-1000">
            <i class="fas fa-lock text-green-400 text-2xl"></i>
        </div>
        <div
            class="absolute top-1/2 left-1/4 w-12 h-12 bg-purple-500/20 rounded-full flex items-center justify-center animate-pulse delay-500">
            <i class="fas fa-key text-purple-400 text-xl"></i>
        </div>
    </section>

    <!-- Overview Section -->
    <section id="overview" class="py-20 bg-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">🔒 Taxly E-Invoicing Ecosystem</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Comprehensive Security Architecture for FIRS Compliance - Powering B2B and B2C Electronic Invoicing
                    Across Nigeria
                </p>
            </div>

            <div class="bg-slate-700/50 p-8 rounded-xl mb-12" data-aos="fade-up">
                <h3 class="text-2xl font-bold text-white mb-4">🌐 The Nigerian E-Invoice Access Point</h3>
                <p class="text-slate-300 mb-6">
                    <strong>Taxly</strong> operates as Nigeria's premier FIRS-certified access point within the 5-corner
                    e-invoice framework. We provide the foundational compliance infrastructure that businesses rely on
                    for secure, validated invoice transmission to tax authorities.
                    <strong>Vendra</strong> handles B2B vendor management, <strong>Akraa</strong> provides specialized
                    industry solutions, and <strong>ATRS</strong> delivers B2C fiscalization for retail and
                    hospitality—all protected by military-grade security architecture.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">FIRS Certified</span>
                    <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm">5-Corner
                        Framework</span>
                    <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-sm">Military-Grade
                        Security</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Business Applications Layer -->
    <section id="applications" class="py-20 bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">📱 Business Applications Layer</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Multi-tier business solutions with comprehensive security integration
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <!-- Vendra -->
                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-xl">✓</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Vendra</h3>
                    </div>
                    <p class="text-slate-300 mb-6">
                        End-to-end vendor lifecycle platform: Streamlined KYC onboarding, purchase order orchestration
                        with FIRS formatting, IRN generation for clients without ERP systems, and bidirectional invoice
                        flow management.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-check text-blue-400 mr-3"></i>
                            <span class="text-sm">Multi-tier access including WhatsApp-native interface</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-check text-blue-400 mr-3"></i>
                            <span class="text-sm">Full-featured enterprise portal</span>
                        </div>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">🔐 SSO Integration</span>
                        <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">🛡️ 2FA Required</span>
                        <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">💬 WhatsApp E2E</span>
                        <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">📊 Bulk CSV</span>
                        <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">🔢 IRN Generation</span>
                    </div>
                </div>

                <!-- Akraa -->
                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-xl">△</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white">Akraa</h3>
                    </div>
                    <p class="text-slate-300 mb-6">
                        Specialized invoicing solution built on Taxly's compliance engine, designed for specific
                        business verticals with industry-tailored workflows and automation.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">🔐 OAuth 2.0</span>
                        <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">🔑 API Access</span>
                        <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">🛡️ MFA</span>
                    </div>
                </div>

                <!-- ATRS -->
                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="300">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                            <span class="text-white font-bold text-xl">🏪</span>
                        </div>
                        <h3 class="text-2xl font-bold text-white">ATRS Fiscalization</h3>
                    </div>
                    <p class="text-slate-300 mb-6">
                        B2C fiscalization platform integrated with hotel PMS systems: Real-time receipt generation,
                        fiscal device connectivity, automated tax reporting, and seamless guest transaction processing
                        with FIRS compliance.
                    </p>
                    <div class="mt-6 flex flex-wrap gap-2">
                        <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded text-xs">🏨 Hotel PMS</span>
                        <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded text-xs">🧾 Real-Time
                            Receipts</span>
                        <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded text-xs">🔐 Device
                            Security</span>
                        <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded text-xs">📊 B2C Compliance</span>
                    </div>
                </div>
            </div>

            <div class="bg-slate-700/50 p-6 rounded-xl" data-aos="fade-up">
                <h4 class="text-lg font-semibold text-blue-400 mb-3">🔒 Application-to-Core Security</h4>
                <p class="text-slate-300">
                    All traffic between business applications and Taxly core uses TLS 1.3 with perfect forward secrecy.
                    Authentication via OAuth 2.0 bearer tokens with 60-minute validity and automatic refresh mechanisms.
                </p>
            </div>
        </div>
    </section>

    <!-- Vendra Platform Components -->
    <section class="py-20 bg-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">🔄 Vendra Platform Components</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Comprehensive vendor management with automated workflows and security integration
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <div class="space-y-6">
                    <!-- Onboarding Engine -->
                    <div class="security-card interactive-hover p-6 rounded-xl" data-aos="fade-up"
                        data-aos-delay="100">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold">✅</span>
                            </div>
                            <h4 class="text-xl font-bold text-white">Onboarding Engine</h4>
                        </div>
                        <p class="text-slate-300 mb-4">
                            Automated vendor verification pipeline: Identity validation, tax compliance verification,
                            financial checks, and risk assessment with minimal manual intervention.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded text-xs">📋 Document
                                OCR</span>
                            <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded text-xs">🔐 Encrypted
                                Vault</span>
                            <span class="bg-green-500/20 text-green-300 px-2 py-1 rounded text-xs">✅ FIRS
                                Verification</span>
                        </div>
                    </div>

                    <!-- PO Orchestration -->
                    <div class="security-card interactive-hover p-6 rounded-xl" data-aos="fade-up"
                        data-aos-delay="200">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold">📄</span>
                            </div>
                            <h4 class="text-xl font-bold text-white">PO Orchestration</h4>
                        </div>
                        <p class="text-slate-300 mb-4">
                            Purchase order lifecycle management: Creation, FIRS-compliant formatting, multi-channel
                            distribution (WhatsApp/Portal), and acknowledgment tracking with deadline enforcement.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">🔄
                                Auto-Transform</span>
                            <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">📱 WhatsApp Bot</span>
                            <span class="bg-blue-500/20 text-blue-300 px-2 py-1 rounded text-xs">✅ Status
                                Tracking</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Invoice Routing -->
                    <div class="security-card interactive-hover p-6 rounded-xl" data-aos="fade-up"
                        data-aos-delay="300">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold">📨</span>
                            </div>
                            <h4 class="text-xl font-bold text-white">Invoice Routing</h4>
                        </div>
                        <p class="text-slate-300 mb-4">
                            Intelligent invoice reception and distribution: Receive FIRS-validated invoices from
                            suppliers, perform three-way matching, and route to enterprise accounting systems with
                            format translation.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">✅ 3-Way
                                Match</span>
                            <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">🔄 ERP
                                Connectors</span>
                            <span class="bg-purple-500/20 text-purple-300 px-2 py-1 rounded text-xs">📊 Format
                                Bridge</span>
                        </div>
                    </div>

                    <!-- WhatsApp Lite -->
                    <div class="security-card interactive-hover p-6 rounded-xl" data-aos="fade-up"
                        data-aos-delay="400">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-indigo-500 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-white font-bold">💬</span>
                            </div>
                            <h4 class="text-xl font-bold text-white">WhatsApp Lite</h4>
                        </div>
                        <p class="text-slate-300 mb-4">
                            Mobile-first vendor experience: Conversational interface for PO confirmations and guided
                            invoice creation - zero software installation required.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-indigo-500/20 text-indigo-300 px-2 py-1 rounded text-xs">📱 Zero
                                Install</span>
                            <span class="bg-indigo-500/20 text-indigo-300 px-2 py-1 rounded text-xs">🔐 SMS
                                Verification</span>
                            <span class="bg-indigo-500/20 text-indigo-300 px-2 py-1 rounded text-xs">✅ Simple UX</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-700/50 p-6 rounded-xl" data-aos="fade-up">
                <h4 class="text-lg font-semibold text-indigo-400 mb-3">🔑 Authentication Framework</h4>
                <p class="text-slate-300">
                    Layered authentication strategy - OAuth 2.0 for web/API clients, SMS-based OTP for WhatsApp users,
                    mandatory 2FA for administrative functions. All sessions expire after 15 minutes of inactivity.
                </p>
            </div>
        </div>
    </section>

    <!-- Security Perimeter -->
    <section id="security" class="py-20 bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">🛡️ Security Perimeter</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Advanced threat protection with multi-layered defense mechanisms
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <div class="threat-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-16 h-16 bg-red-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-fire text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">🔥 Threat Protection</h3>
                    <p class="text-slate-300 mb-4">
                        Advanced threat detection and mitigation: Machine learning-powered WAF, volumetric DDoS
                        absorption, and intelligent bot filtering with challenge mechanisms.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-red-400 mr-2"></i>
                            <span class="text-sm">ML-Powered WAF</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-red-400 mr-2"></i>
                            <span class="text-sm">DDoS Shield</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-red-400 mr-2"></i>
                            <span class="text-sm">Bot Defense</span>
                        </div>
                    </div>
                </div>

                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-door-open text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">🚪 API Gateway</h3>
                    <p class="text-slate-300 mb-4">
                        Centralized request management: Token validation, request throttling, payload inspection, and
                        response sanitization with zero-trust verification.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-blue-400 mr-2"></i>
                            <span class="text-sm">JWT Verify</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-blue-400 mr-2"></i>
                            <span class="text-sm">Rate Control</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-blue-400 mr-2"></i>
                            <span class="text-sm">Schema Check</span>
                        </div>
                    </div>
                </div>

                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-balance-scale text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">⚖️ Load Distribution</h3>
                    <p class="text-slate-300 mb-4">
                        High-availability traffic management: Geographic distribution across multiple zones with
                        health-aware routing and automatic failover.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span class="text-sm">Multi-Zone</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span class="text-sm">Health Aware</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span class="text-sm">Auto-Failover</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-700/50 p-8 rounded-xl" data-aos="fade-up">
                <h3 class="text-2xl font-bold text-white mb-4">🔐 Perimeter Defense</h3>
                <p class="text-slate-300 mb-6">
                    Three-tier security filtering - Layer 7 application firewall blocks injection attacks, API gateway
                    enforces business logic, and load balancer maintains availability targets exceeding 99.9% uptime.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <h4 class="font-semibold text-red-400 mb-2">Layer 7 WAF</h4>
                        <p class="text-slate-400 text-sm">Blocks injection attacks and malicious payloads</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-cogs text-white"></i>
                        </div>
                        <h4 class="font-semibold text-blue-400 mb-2">API Gateway</h4>
                        <p class="text-slate-400 text-sm">Enforces business logic and schema validation</p>
                    </div>
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-server text-white"></i>
                        </div>
                        <h4 class="font-semibold text-green-400 mb-2">Load Balancer</h4>
                        <p class="text-slate-400 text-sm">Maintains high availability and performance</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Taxly Core Platform -->
    <section class="py-20 bg-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">⚙️ Taxly Core Platform</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    FIRS-certified infrastructure with cryptographic security and government integration
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                <div class="compliance-card interactive-hover p-8 rounded-xl" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-rocket text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">🚀 Taxly Compliance Engine</h3>
                    <p class="text-slate-300 mb-4">
                        FIRS-certified access point infrastructure: Accept invoices from any source, enforce Nigerian
                        tax authority specifications, apply cryptographic signatures, and guarantee delivery to
                        government systems with full audit trails.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span class="text-sm">FIRS Certified</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span class="text-sm">Schema Enforcement</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span class="text-sm">Complete Audit</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-green-400 mr-2"></i>
                            <span class="text-sm">Format Bridge</span>
                        </div>
                    </div>
                </div>

                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-16 h-16 bg-purple-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-signature text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">✍️ Cryptographic Signing</h3>
                    <p class="text-slate-300 mb-4">
                        Hardware-secured signature generation: PKI-based invoice signing using FIPS 140-2 Level 3
                        certified hardware security modules with private keys that never enter software memory.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">HSM-Backed</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">PKI Chain</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-purple-400 mr-2"></i>
                            <span class="text-sm">Timestamp</span>
                        </div>
                    </div>
                </div>

                <div class="compliance-card interactive-hover p-8 rounded-xl" data-aos="fade-up"
                    data-aos-delay="300">
                    <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-qrcode text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-4">📱 Verification Codes</h3>
                    <p class="text-slate-300 mb-4">
                        Tamper-evident QR generation: Cryptographically-bound visual codes containing signature
                        references, IRN identifiers, and public verification URLs.
                    </p>
                    <div class="space-y-2">
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-blue-400 mr-2"></i>
                            <span class="text-sm">Crypto-Bound</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-blue-400 mr-2"></i>
                            <span class="text-sm">Tamper-Proof</span>
                        </div>
                        <div class="flex items-center text-slate-400">
                            <i class="fas fa-check text-blue-400 mr-2"></i>
                            <span class="text-sm">Public Verify</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-700/50 p-8 rounded-xl" data-aos="fade-up">
                <h3 class="text-2xl font-bold text-white mb-4">✍️ Digital Signature Chain</h3>
                <p class="text-slate-300 mb-6">
                    Complete non-repudiation guaranteed through SHA-256 cryptographic hashing → Hardware security module
                    signing with 2048-bit RSA → PKCS#7 standardized envelope → RFC 3161 timestamp authority
                    verification.
                </p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-white font-bold">1</span>
                        </div>
                        <h4 class="font-semibold text-green-400 text-sm">SHA-256 Hash</h4>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-white font-bold">2</span>
                        </div>
                        <h4 class="font-semibold text-blue-400 text-sm">HSM Signing</h4>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div
                            class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-white font-bold">3</span>
                        </div>
                        <h4 class="font-semibold text-purple-400 text-sm">PKCS#7 Envelope</h4>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div
                            class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <span class="text-white font-bold">4</span>
                        </div>
                        <h4 class="font-semibold text-orange-400 text-sm">Timestamp Authority</h4>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Government Integration -->
    <section class="py-20 bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">🏛️ Government Integration</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Secure connection to FIRS with fault-tolerant submission and resilience mechanisms
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <div class="compliance-card interactive-hover p-8 rounded-xl" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-university text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">🏛️ FIRS Connection</h3>
                    </div>
                    <p class="text-slate-300 mb-6">
                        Dedicated government gateway: Private network tunnel to Federal Inland Revenue Service with
                        mutual certificate authentication and connection-level encryption.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-lock text-green-400 mr-3"></i>
                            <span class="text-sm">Private Tunnel</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-certificate text-green-400 mr-3"></i>
                            <span class="text-sm">Mutual TLS</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-shield-alt text-green-400 mr-3"></i>
                            <span class="text-sm">IP Restricted</span>
                        </div>
                    </div>
                </div>

                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-sync-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">🔄 Resilience Layer</h3>
                    </div>
                    <p class="text-slate-300 mb-6">
                        Fault-tolerant submission: Intelligent retry logic with exponential backoff, circuit breaker
                        patterns, and automated daily reconciliation against FIRS records.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-redo text-blue-400 mr-3"></i>
                            <span class="text-sm">Smart Retry</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-bolt text-blue-400 mr-3"></i>
                            <span class="text-sm">Circuit Break</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-check-double text-blue-400 mr-3"></i>
                            <span class="text-sm">Auto Reconcile</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-700/50 p-8 rounded-xl" data-aos="fade-up">
                <h3 class="text-2xl font-bold text-white mb-4">🏛️ FIRS Protocol</h3>
                <p class="text-slate-300 mb-6">
                    Government-issued X.509 certificates enable mutual authentication. Dedicated VPN tunnel isolated
                    from public internet. Source IP verification prevents unauthorized access. Circuit breaker activates
                    after 5 consecutive failures.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-certificate text-white"></i>
                        </div>
                        <h4 class="font-semibold text-green-400 mb-2">X.509 Certificates</h4>
                        <p class="text-slate-400 text-sm">Government-issued for mutual authentication</p>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-network-wired text-white"></i>
                        </div>
                        <h4 class="font-semibold text-blue-400 mb-2">Dedicated VPN</h4>
                        <p class="text-slate-400 text-sm">Isolated from public internet</p>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-shield-alt text-white"></i>
                        </div>
                        <h4 class="font-semibold text-purple-400 mb-2">Circuit Breaker</h4>
                        <p class="text-slate-400 text-sm">Activates after 5 consecutive failures</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Compliance & Archival -->
    <section class="py-20 bg-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">💾 Compliance & Archival</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Regulatory-compliant storage with immutable records and comprehensive audit packages
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <div class="compliance-card interactive-hover p-8 rounded-xl" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-lock text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">🔒 Immutable Archive</h3>
                    </div>
                    <p class="text-slate-300 mb-6">
                        Regulatory-compliant storage: Write-once-read-many architecture with cryptographic integrity
                        seals, maintaining unalterable records for 7-year mandatory retention period.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-key text-green-400 mr-3"></i>
                            <span class="text-sm">AES-256 Encryption</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-shield-alt text-green-400 mr-3"></i>
                            <span class="text-sm">Tamper-Seal</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-clock text-green-400 mr-3"></i>
                            <span class="text-sm">7-Year Retention</span>
                        </div>
                    </div>
                </div>

                <div class="security-card interactive-hover p-8 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white">📋 Evidence Bundles</h3>
                    </div>
                    <p class="text-slate-300 mb-6">
                        Comprehensive audit packages: Complete transaction documentation including original submission,
                        signatures, government receipts, timestamps, and processing logs - exportable in standard
                        formats.
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-box text-blue-400 mr-3"></i>
                            <span class="text-sm">Full Package</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-chart-bar text-blue-400 mr-3"></i>
                            <span class="text-sm">Multi-Format</span>
                        </div>
                        <div class="flex items-center text-slate-300">
                            <i class="fas fa-search text-blue-400 mr-3"></i>
                            <span class="text-sm">Searchable</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-700/50 p-8 rounded-xl" data-aos="fade-up">
                <h3 class="text-2xl font-bold text-white mb-4">💾 Archive Integrity</h3>
                <p class="text-slate-300 mb-6">
                    SHA-256 checksums computed at write-time prevent silent data corruption. Physical separation from
                    operational systems. Geo-redundant replication within Nigerian territory. Automatic lifecycle
                    management enforces retention policies.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-hashtag text-white"></i>
                        </div>
                        <h4 class="font-semibold text-green-400 text-sm">SHA-256</h4>
                        <p class="text-slate-400 text-xs">Integrity checksums</p>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-server text-white"></i>
                        </div>
                        <h4 class="font-semibold text-blue-400 text-sm">Physical Separation</h4>
                        <p class="text-slate-400 text-xs">From operational systems</p>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div
                            class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-globe text-white"></i>
                        </div>
                        <h4 class="font-semibold text-purple-400 text-sm">Geo-Redundant</h4>
                        <p class="text-slate-400 text-xs">Within Nigeria</p>
                    </div>
                    <div class="text-center p-4 bg-slate-800/50 rounded-lg">
                        <div
                            class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-calendar-alt text-white"></i>
                        </div>
                        <h4 class="font-semibold text-orange-400 text-sm">Lifecycle Management</h4>
                        <p class="text-slate-400 text-xs">Automatic enforcement</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Operations -->
    <section id="controls" class="py-20 bg-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">👁️ Security Operations</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    Real-time monitoring, threat intelligence, and comprehensive audit systems
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                <div class="threat-card interactive-hover p-6 rounded-xl" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">📊 Threat Intelligence</h3>
                    <p class="text-slate-300 text-sm mb-4">
                        Real-time security analytics: Centralized log aggregation, behavioral anomaly detection, and
                        correlation across all system components with automated incident escalation.
                    </p>
                    <div class="space-y-1">
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-bolt text-orange-400 mr-2"></i>
                            <span>Real-Time</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-brain text-orange-400 mr-2"></i>
                            <span>ML Detection</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-link text-orange-400 mr-2"></i>
                            <span>Correlation</span>
                        </div>
                    </div>
                </div>

                <div class="security-card interactive-hover p-6 rounded-xl" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-clipboard-list text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">📝 Audit System</h3>
                    <p class="text-slate-300 text-sm mb-4">
                        Tamper-evident logging: Cryptographically-chained audit records with 90-day immediate access and
                        long-term cold storage for regulatory requirements.
                    </p>
                    <div class="space-y-1">
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-link text-blue-400 mr-2"></i>
                            <span>Hash Chain</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-lock text-blue-400 mr-2"></i>
                            <span>Encrypted</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-clock text-blue-400 mr-2"></i>
                            <span>Timestamped</span>
                        </div>
                    </div>
                </div>

                <div class="threat-card interactive-hover p-6 rounded-xl" data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">🛡️ Intrusion Defense</h3>
                    <p class="text-slate-300 text-sm mb-4">
                        Multi-layer threat detection: Network and host-based intrusion detection with signature
                        matching, anomaly detection, and automated response capabilities.
                    </p>
                    <div class="space-y-1">
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-network-wired text-red-400 mr-2"></i>
                            <span>Network IDS</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-desktop text-red-400 mr-2"></i>
                            <span>Host EDR</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-bolt text-red-400 mr-2"></i>
                            <span>Auto-Block</span>
                        </div>
                    </div>
                </div>

                <div class="compliance-card interactive-hover p-6 rounded-xl" data-aos="fade-up"
                    data-aos-delay="400">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-sync-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">💾 Disaster Recovery</h3>
                    <p class="text-slate-300 text-sm mb-4">
                        Business continuity assurance: Continuous replication with 15-minute recovery point objective
                        and 2-hour recovery time objective backed by quarterly disaster recovery exercises.
                    </p>
                    <div class="space-y-1">
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-sync text-green-400 mr-2"></i>
                            <span>Continuous</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-lock text-green-400 mr-2"></i>
                            <span>Encrypted</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-globe text-green-400 mr-2"></i>
                            <span>Multi-Region</span>
                        </div>
                    </div>
                </div>

                <div class="security-card interactive-hover p-6 rounded-xl" data-aos="fade-up" data-aos-delay="500">
                    <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-database text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">💾 Data Protection</h3>
                    <p class="text-slate-300 text-sm mb-4">
                        Comprehensive data security with encryption at rest and in transit, access controls, and regular
                        security assessments.
                    </p>
                    <div class="space-y-1">
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-key text-purple-400 mr-2"></i>
                            <span>AES-256</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-user-shield text-purple-400 mr-2"></i>
                            <span>Access Control</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-clipboard-check text-purple-400 mr-2"></i>
                            <span>Compliance</span>
                        </div>
                    </div>
                </div>

                <div class="threat-card interactive-hover p-6 rounded-xl" data-aos="fade-up" data-aos-delay="600">
                    <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-eye text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-white mb-3">👁️ Monitoring</h3>
                    <p class="text-slate-300 text-sm mb-4">
                        24/7 security monitoring with SIEM integration, automated alerting, and incident response
                        procedures.
                    </p>
                    <div class="space-y-1">
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-chart-bar text-yellow-400 mr-2"></i>
                            <span>SIEM Integration</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-bell text-yellow-400 mr-2"></i>
                            <span>Auto Alerting</span>
                        </div>
                        <div class="flex items-center text-slate-400 text-xs">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                            <span>Incident Response</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Controls Matrix -->
            <div class="bg-slate-800/50 p-8 rounded-xl" data-aos="fade-up">
                <h3 class="text-2xl font-bold text-white mb-6">🔐 Security Controls Reference</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-lock text-white text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-blue-400 mb-3">Transport Security</h4>
                        <p class="text-slate-400 text-sm">TLS 1.3 with forward secrecy and certificate pinning</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-key text-white text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-purple-400 mb-3">Cryptography</h4>
                        <p class="text-slate-400 text-sm">AES-256 storage, RSA-2048 signatures, SHA-256 hashing</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-fingerprint text-white text-2xl"></i>
                        </div>
                        <h4 class="font-semibold text-green-400 mb-3">Identity</h4>
                        <p class="text-slate-400 text-sm">OAuth 2.0, mutual TLS, MFA, SMS OTP, WhatsApp E2E</p>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <div class="w-16 h-16 bg-orange-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-check text-white text-2xl"></i>
                    </div>
                    <h4 class="font-semibold text-orange-400 mb-3">Compliance</h4>
                    <p class="text-slate-400 text-sm">Hash-chained logs, WORM archive, SIEM correlation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Complete Security Flow -->
    <section id="flow" class="py-20 bg-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">🔒 Complete Security Flow (B2B & B2C)</h2>
                <p class="text-xl text-slate-400 max-w-3xl mx-auto">
                    End-to-end security process from client authentication to immutable archival
                </p>
            </div>

            <div class="space-y-8">
                <!-- Step 1 -->
                <div class="flex items-start space-x-6" data-aos="fade-right">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">1</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">👤 Client Authentication</h3>
                        <p class="text-slate-300 mb-4">
                            Users authenticate via OAuth 2.0 (enterprises), SMS OTP (WhatsApp), or API keys (system
                            integrations). Mandatory 2FA for privileged operations.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded text-xs">OAuth 2.0</span>
                            <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded text-xs">SMS OTP</span>
                            <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded text-xs">API Keys</span>
                            <span class="bg-red-500/20 text-red-300 px-3 py-1 rounded text-xs">Mandatory 2FA</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex items-start space-x-6" data-aos="fade-left">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">2</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">✍️ Vendra: PO Creation</h3>
                        <p class="text-slate-300 mb-4">
                            Enterprise creates purchase order through web portal or bulk CSV upload. Automatic
                            transformation into FIRS-compliant XML structure.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded text-xs">Web Portal</span>
                            <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded text-xs">Bulk CSV</span>
                            <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded text-xs">FIRS XML</span>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex items-start space-x-6" data-aos="fade-right">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">3</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">📱 Vendor Delivery</h3>
                        <p class="text-slate-300 mb-4">
                            PO transmitted via WhatsApp bot (Lite) or vendor portal (Enterprise) with end-to-end
                            encryption and delivery confirmation.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded text-xs">WhatsApp Bot</span>
                            <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded text-xs">Vendor Portal</span>
                            <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded text-xs">E2E
                                Encryption</span>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex items-start space-x-6" data-aos="fade-left">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">4</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">💬 Vendor Response</h3>
                        <p class="text-slate-300 mb-4">
                            Supplier acknowledges PO and creates invoice using conversational WhatsApp interface or
                            structured web forms.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded text-xs">Conversational
                                UI</span>
                            <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded text-xs">Web Forms</span>
                            <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded text-xs">Zero
                                Install</span>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex items-start space-x-6" data-aos="fade-right">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">5</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">🔐 Encrypted Transit</h3>
                        <p class="text-slate-300 mb-4">
                            Invoice data travels over TLS 1.3 encrypted channels with perfect forward secrecy and
                            certificate pinning.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded text-xs">TLS 1.3</span>
                            <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded text-xs">Perfect
                                Secrecy</span>
                            <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded text-xs">Cert
                                Pinning</span>
                        </div>
                    </div>
                </div>

                <!-- Step 6 -->
                <div class="flex items-start space-x-6" data-aos="fade-left">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">6</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">🛡️ Perimeter Security</h3>
                        <p class="text-slate-300 mb-4">
                            Traffic passes through WAF (injection attack prevention), API gateway (schema validation),
                            and rate limiters (100 req/min per client).
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-red-500/20 text-red-300 px-3 py-1 rounded text-xs">WAF Protection</span>
                            <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded text-xs">Schema
                                Validation</span>
                            <span class="bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded text-xs">Rate
                                Limiting</span>
                        </div>
                    </div>
                </div>

                <!-- Step 7 -->
                <div class="flex items-start space-x-6" data-aos="fade-right">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">7</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">🔍 Taxly Validation</h3>
                        <p class="text-slate-300 mb-4">
                            Core engine verifies FIRS compliance, validates tax calculations, checks business rules, and
                            rejects non-conforming submissions.
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded text-xs">FIRS
                                Compliance</span>
                            <span class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded text-xs">Tax Validation</span>
                            <span class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded text-xs">Business
                                Rules</span>
                        </div>
                    </div>
                </div>

                <!-- Step 8 -->
                <div class="flex items-start space-x-6" data-aos="fade-left">
                    <div class="flow-step w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold">8</span>
                    </div>
                    <div class="bg-slate-700/50 p-6 rounded-xl flex-1">
                        <h3 class="text-xl font-bold text-white mb-3">📦 Finalize & Archive</h3>
                        <p class="text-slate-300 mb-4">
                            Final validation and archival to immutable storage.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="border-t border-slate-800 mt-8 pt-8 text-center text-slate-400">
                <p>&copy; 2025 Taxly. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100
        });

        // Mobile menu toggle (ensure elements exist)
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    if (mobileMenu) mobileMenu.classList.add('hidden');
                }
            });
        });
    </script>
</body>

</html>
