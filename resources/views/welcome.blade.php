<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Taxly - Modern Tax Management System</title>
    <meta name="description"
        content="Taxly is a comprehensive tax management system with FIRS integration, automated invoicing, and real-time compliance monitoring.">

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
        }

        .gradient-bg {
            background: linear-gradient(135deg, #2FA838 0%, #1a7a26 100%);
        }

        .gradient-text {
            background: linear-gradient(135deg, #2FA838 0%, #1a7a26 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            background: linear-gradient(135deg, #2FA838 0%, #1a7a26 100%);
        }

        .tech-badge {
            background: rgba(47, 168, 56, 0.1);
            color: #2FA838;
        }

        .code-block {
            background: #1e293b;
            color: #e2e8f0;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="fixed top-0 w-full bg-white/90 backdrop-blur-md z-50 border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('logo.png') }}" alt="no logo" class="h-10 w-auto">
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="#features"
                            class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Features</a>
                        <a href="#architecture"
                            class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Architecture</a>
                        <a href="#api"
                            class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">API</a>
                        <a href="#deployment"
                            class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">Deployment</a>
                        <a href="#docs"
                            class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 transition-colors">Documentation</a>
                    </div>
                </div>
                <div class="md:hidden">
                    <button type="button"
                        class="text-gray-700 hover:text-green-600 focus:outline-none focus:text-green-600"
                        id="mobile-menu-button">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="hidden md:hidden bg-white border-t border-gray-200" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#features" class="block px-3 py-2 text-gray-700 hover:text-green-600">Features</a>
                <a href="#architecture" class="block px-3 py-2 text-gray-700 hover:text-green-600">Architecture</a>
                <a href="#api" class="block px-3 py-2 text-gray-700 hover:text-green-600">API</a>
                <a href="#deployment" class="block px-3 py-2 text-gray-700 hover:text-green-600">Deployment</a>
                <a href="#docs" class="block px-3 py-2 text-green-600 font-medium">Documentation</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="gradient-bg min-h-screen flex items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center" data-aos="fade-up">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Modern Tax Management
                    <span class="block text-yellow-300">Made Simple</span>
                </h1>
                <p class="text-xl text-gray-200 mb-8 max-w-3xl mx-auto">
                    Taxly is a comprehensive tax management system with FIRS integration, automated invoicing,
                    real-time compliance monitoring, and enterprise-grade security.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#features"
                        class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Explore Features
                    </a>
                    <a href="#api"
                        class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-green-600 transition-colors">
                        View API Docs
                    </a>
                </div>
            </div>
        </div>

        <!-- Floating elements -->
        <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-32 h-32 bg-white/10 rounded-full animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/4 w-16 h-16 bg-white/10 rounded-full animate-pulse delay-500"></div>
    </section>

    <!-- Security Ecosystem Section -->
    <section
        class="py-20 bg-gradient-to-br from-slate-900 via-green-900 to-emerald-900 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black/30"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-5xl font-bold mb-6">
                    <span class="bg-gradient-to-r from-green-400 to-emerald-400 bg-clip-text text-transparent">
                        Military-Grade Security Architecture
                    </span>
                </h2>
                <p class="text-xl text-slate-300 max-w-4xl mx-auto mb-8">
                    Experience the most comprehensive security ecosystem for e-invoicing in Nigeria.
                    Built with FIRS compliance, cryptographic signing, and enterprise-grade protection.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/ecosystem"
                        class="bg-green-600 text-white px-8 py-4 rounded-xl font-semibold hover:bg-green-700 transition-all duration-300 transform hover:scale-105 flex items-center">
                        <i class="fas fa-shield-alt mr-3"></i>
                        Explore Security Ecosystem
                    </a>
                    <a href="#features"
                        class="border-2 border-green-400 text-green-400 px-8 py-4 rounded-xl font-semibold hover:bg-green-400 hover:text-white transition-all duration-300">
                        View Features
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-xl border border-white/20" data-aos="fade-up"
                    data-aos-delay="100">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-lock text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">🔐 End-to-End Encryption</h3>
                    <p class="text-slate-300">TLS 1.3 with perfect forward secrecy, certificate pinning, and zero-trust
                        architecture protecting every transaction.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-xl border border-white/20" data-aos="fade-up"
                    data-aos-delay="200">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">✍️ Cryptographic Signing</h3>
                    <p class="text-slate-300">FIPS 140-2 Level 3 HSM-backed digital signatures with PKCS#7 envelopes and
                        RFC 3161 timestamp authority.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-sm p-8 rounded-xl border border-white/20" data-aos="fade-up"
                    data-aos-delay="300">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-6">
                        <i class="fas fa-university text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">🏛️ FIRS Integration</h3>
                    <p class="text-slate-300">Direct government connection with mutual TLS authentication, dedicated
                        VPN tunnels, and automated compliance validation.</p>
                </div>
            </div>

            <div class="mt-12 text-center" data-aos="fade-up">
                <div class="inline-flex items-center space-x-6 text-slate-400">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt text-green-400 mr-2"></i>
                        <span>99.9% Uptime SLA</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-blue-400 mr-2"></i>
                        <span>24/7 Monitoring</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-database text-purple-400 mr-2"></i>
                        <span>7-Year Retention</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-globe text-teal-400 mr-2"></i>
                        <span>Geo-Redundant</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Powerful Features</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to manage taxes efficiently with cutting-edge technology and seamless
                    integrations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- FIRS Integration -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="feature-icon w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-link text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">FIRS Integration</h3>
                    <p class="text-gray-600 mb-4">
                        Seamless integration with Federal Inland Revenue Service for real-time tax validation,
                        invoice transmission, and compliance monitoring.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Real-time IRN validation</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Automated invoice submission</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Tax compliance monitoring</li>
                    </ul>
                </div>

                <!-- Automated Invoicing -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100" data-aos="fade-up"
                    data-aos-delay="200">
                    <div class="feature-icon w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-file-invoice text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Automated Invoicing</h3>
                    <p class="text-gray-600 mb-4">
                        Generate, validate, and manage invoices automatically with intelligent numbering,
                        tax calculations, and digital signatures.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Smart invoice numbering</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Automatic tax calculations</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Digital signatures & QR codes</li>
                    </ul>
                </div>

                <!-- Multi-tenancy -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100" data-aos="fade-up"
                    data-aos-delay="300">
                    <div class="feature-icon w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-building text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Multi-Tenancy</h3>
                    <p class="text-gray-600 mb-4">
                        Support multiple organizations with complete data isolation,
                        role-based access control, and tenant-specific configurations.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Complete data isolation</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Role-based permissions</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Tenant-specific settings</li>
                    </ul>
                </div>

                <!-- Real-time Processing -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100" data-aos="fade-up"
                    data-aos-delay="400">
                    <div class="feature-icon w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-bolt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Real-time Processing</h3>
                    <p class="text-gray-600 mb-4">
                        Process invoices and tax submissions in real-time with Redis-powered
                        queuing system and background job processing.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Redis-powered queues</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Background job processing</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Real-time notifications</li>
                    </ul>
                </div>

                <!-- API & Webhooks -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100" data-aos="fade-up"
                    data-aos-delay="500">
                    <div class="feature-icon w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-plug text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">API & Webhooks</h3>
                    <p class="text-gray-600 mb-4">
                        RESTful API with Swagger documentation and webhook system for
                        real-time integrations with external systems.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>RESTful API with Swagger</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Webhook notifications</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>API key authentication</li>
                    </ul>
                </div>

                <!-- Enterprise Security -->
                <div class="card-hover bg-white p-8 rounded-xl shadow-lg border border-gray-100" data-aos="fade-up"
                    data-aos-delay="600">
                    <div class="feature-icon w-16 h-16 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Enterprise Security</h3>
                    <p class="text-gray-600 mb-4">
                        Bank-level security with encrypted data storage, audit trails,
                        and comprehensive access control mechanisms.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2">
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Encrypted data storage</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Comprehensive audit trails</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i>Role-based access control</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Architecture Section -->
    <section id="architecture" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Security Architecture</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Military-grade security framework with FIRS compliance, cryptographic protection, and
                    enterprise-grade safeguards.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-12">
                <div data-aos="fade-right">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Multi-Layer Security Framework</h3>

                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div
                                class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-shield-alt text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Threat Protection Layer</h4>
                                <p class="text-gray-600">ML-powered WAF, DDoS shield, and intelligent bot filtering
                                    with challenge mechanisms</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-door-open text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">API Gateway Security</h4>
                                <p class="text-gray-600">JWT validation, rate limiting, schema enforcement, and
                                    zero-trust verification</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-lock text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Cryptographic Protection</h4>
                                <p class="text-gray-600">FIPS 140-2 Level 3 HSM, RSA-2048 signatures, PKCS#7 envelopes,
                                    and RFC 3161 timestamps</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-university text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Government Integration</h4>
                                <p class="text-gray-600">Mutual TLS with FIRS, dedicated VPN tunnels, and automated
                                    compliance validation</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div
                                class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-archive text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Immutable Compliance</h4>
                                <p class="text-gray-600">WORM storage, SHA-256 integrity seals, 7-year retention, and
                                    hash-chained audit trails</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="/ecosystem"
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>
                            View Complete Security Architecture
                        </a>
                    </div>
                </div>

                <div data-aos="fade-left">
                    <div class="bg-gradient-to-br from-slate-900 to-green-900 p-8 rounded-xl text-white">
                        <h4 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-certificate text-yellow-400 mr-3"></i>
                            Security Certifications & Standards
                        </h4>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">Transport Security</span>
                                <span class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm">TLS
                                    1.3</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">Encryption Standard</span>
                                <span
                                    class="bg-blue-500/20 text-blue-300 px-3 py-1 rounded-full text-sm">AES-256</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">Digital Signatures</span>
                                <span
                                    class="bg-purple-500/20 text-purple-300 px-3 py-1 rounded-full text-sm">RSA-2048</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">Hash Algorithm</span>
                                <span
                                    class="bg-orange-500/20 text-orange-300 px-3 py-1 rounded-full text-sm">SHA-256</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">HSM Certification</span>
                                <span class="bg-red-500/20 text-red-300 px-3 py-1 rounded-full text-sm">FIPS 140-2
                                    L3</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-300">Uptime SLA</span>
                                <span
                                    class="bg-green-500/20 text-green-300 px-3 py-1 rounded-full text-sm">99.9%</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-slate-700">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-slate-400">Data Retention</span>
                                <span class="text-slate-300">7 Years</span>
                            </div>
                            <div class="flex items-center justify-between text-sm mt-2">
                                <span class="text-slate-400">Audit Trail</span>
                                <span class="text-slate-300">Immutable</span>
                            </div>
                            <div class="flex items-center justify-between text-sm mt-2">
                                <span class="text-slate-400">Compliance</span>
                                <span class="text-slate-300">FIRS Certified</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                            <div class="text-2xl font-bold text-green-600">14</div>
                            <div class="text-sm text-gray-600">Security Layers</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border border-gray-200 text-center">
                            <div class="text-2xl font-bold text-green-600">24/7</div>
                            <div class="text-sm text-gray-600">Monitoring</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-50 to-orange-50 p-8 rounded-xl border border-red-200"
                data-aos="fade-up">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-3"></i>
                    Complete Security Flow
                </h3>
                <p class="text-gray-700 mb-6">
                    From client authentication to immutable archival, every transaction follows a 14-step security
                    process
                    including OAuth 2.0/SMS OTP authentication, TLS 1.3 encryption, FIRS validation, cryptographic
                    signing,
                    and government clearance with dedicated VPN tunnels.
                </p>
                <div class="flex flex-wrap gap-3">
                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">OAuth 2.0</span>
                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm">TLS 1.3</span>
                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">FIPS 140-2</span>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">FIRS Certified</span>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">PKCS#7</span>
                    <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">WORM Archive</span>
                </div>
            </div>
        </div>
    </section>

    <!-- API Section -->
    <section id="api" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">RESTful API</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Comprehensive API with Swagger documentation for seamless integrations.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2" data-aos="fade-right">
                    <div class="code-block p-6 rounded-xl mb-6">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-gray-400 text-sm ml-2">POST /api/invoices</span>
                        </div>
                        <pre class="text-sm overflow-x-auto"><code>{
  "invoice": {
    "business_id": "ORG123",
    "irn": "INV000001-ORG123-20241128",
    "issue_date": "2024-11-28",
    "due_date": "2024-12-28",
    "invoice_type_code": "380",
    "payment_status": "Pending",
    "tax_total": {
      "tax_amount": 1500.00,
      "tax_currency": "NGN"
    },
    "legal_monetary_total": {
      "line_extension_amount": 10000.00,
      "tax_exclusive_amount": 10000.00,
      "tax_inclusive_amount": 11500.00
    }
  }
}</code></pre>
                    </div>

                    <div class="code-block p-6 rounded-xl">
                        <div class="flex items-center space-x-2 mb-4">
                            <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                            <span class="text-gray-400 text-sm ml-2">Response</span>
                        </div>
                        <pre class="text-sm overflow-x-auto"><code>{
  "success": true,
  "data": {
    "id": 123,
    "invoice_number": "INV000001",
    "status": "validated",
    "irn": "INV000001-ORG123-20241128",
    "qr_code": "data:image/png;base64,...",
    "created_at": "2024-11-28T10:30:00Z"
  },
  "message": "Invoice created and validated successfully"
}</code></pre>
                    </div>
                </div>

                <div data-aos="fade-left">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">API Features</h3>

                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">Invoice CRUD operations</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">FIRS validation & submission</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">Customer management</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">Organization management</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">Webhook endpoints</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">API key authentication</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-gray-700">Rate limiting</span>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="/api/documentation"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View API Docs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Deployment Section -->
    <section id="deployment" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Cloud-Native Deployment</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Production-ready deployment with Docker, Kubernetes, and CI/CD automation.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="card-hover bg-white p-6 rounded-xl shadow-lg text-center" data-aos="fade-up"
                    data-aos-delay="100">
                    <i class="fab fa-docker text-4xl text-blue-500 mb-4"></i>
                    <h3 class="font-bold text-gray-900 mb-2">Docker</h3>
                    <p class="text-gray-600 text-sm">Containerized application with multi-stage builds</p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg text-center" data-aos="fade-up"
                    data-aos-delay="200">
                    <i class="fas fa-dharmachakra text-4xl text-blue-600 mb-4"></i>
                    <h3 class="font-bold text-gray-900 mb-2">Kubernetes</h3>
                    <p class="text-gray-600 text-sm">Auto-scaling with HPA and load balancing</p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg text-center" data-aos="fade-up"
                    data-aos-delay="300">
                    <i class="fab fa-github text-4xl text-gray-800 mb-4"></i>
                    <h3 class="font-bold text-gray-900 mb-2">GitHub Actions</h3>
                    <p class="text-gray-600 text-sm">Automated CI/CD with DigitalOcean integration</p>
                </div>

                <div class="card-hover bg-white p-6 rounded-xl shadow-lg text-center" data-aos="fade-up"
                    data-aos-delay="400">
                    <i class="fas fa-shield-alt text-4xl text-green-500 mb-4"></i>
                    <h3 class="font-bold text-gray-900 mb-2">SSL & Security</h3>
                    <p class="text-gray-600 text-sm">Let's Encrypt certificates and security best practices</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-8" data-aos="fade-up">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Deployment</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Using Deployment Script</h4>
                        <div class="code-block p-4 rounded-lg">
                            <pre class="text-sm"><code># Clone and deploy
git clone https://github.com/your-repo/taxly.git
cd taxly/k8s
./deploy.sh</code></pre>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-4">Manual Deployment</h4>
                        <div class="code-block p-4 rounded-lg">
                            <pre class="text-sm"><code># Build and push Docker image
docker build -t taxly:latest .
docker push registry.digitalocean.com/taxly/app:latest

# Deploy to Kubernetes
kubectl apply -k .</code></pre>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap gap-4">
                    <span class="tech-badge px-3 py-1 rounded-full text-sm">Auto-scaling</span>
                    <span class="tech-badge px-3 py-1 rounded-full text-sm">Load Balancing</span>
                    <span class="tech-badge px-3 py-1 rounded-full text-sm">SSL Termination</span>
                    <span class="tech-badge px-3 py-1 rounded-full text-sm">Health Checks</span>
                    <span class="tech-badge px-3 py-1 rounded-full text-sm">Rolling Updates</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Documentation Section -->
    <section id="docs" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Comprehensive Documentation</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Everything you need to get started, from installation to advanced configurations.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="card-hover bg-gradient-to-br from-green-50 to-emerald-50 p-8 rounded-xl border border-green-100"
                    data-aos="fade-up" data-aos-delay="100">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-book text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Getting Started</h3>
                    <p class="text-gray-600 mb-6">
                        Step-by-step guide to install, configure, and run Taxly in your environment.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2 mb-6">
                        <li><i class="fas fa-arrow-right text-green-500 mr-2"></i>Installation guide</li>
                        <li><i class="fas fa-arrow-right text-green-500 mr-2"></i>Environment setup</li>
                        <li><i class="fas fa-arrow-right text-green-500 mr-2"></i>Configuration options</li>
                    </ul>
                    <a href="#" class="text-green-600 font-semibold hover:text-green-700">
                        Read Guide <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="card-hover bg-gradient-to-br from-green-50 to-emerald-50 p-8 rounded-xl border border-green-100"
                    data-aos="fade-up" data-aos-delay="200">
                    <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-code text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">API Reference</h3>
                    <p class="text-gray-600 mb-6">
                        Complete API documentation with examples, authentication, and best practices.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2 mb-6">
                        <li><i class="fas fa-arrow-right text-green-500 mr-2"></i>Endpoint documentation</li>
                        <li><i class="fas fa-arrow-right text-green-500 mr-2"></i>Request/Response examples</li>
                        <li><i class="fas fa-arrow-right text-green-500 mr-2"></i>Authentication guide</li>
                    </ul>
                    <a href="/api/documentation" class="text-green-600 font-semibold hover:text-green-700">
                        View Docs <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="card-hover bg-gradient-to-br from-teal-50 to-cyan-50 p-8 rounded-xl border border-teal-100"
                    data-aos="fade-up" data-aos-delay="300">
                    <div class="w-12 h-12 bg-teal-500 rounded-lg flex items-center justify-center mb-6">
                        <i class="fas fa-cloud text-white text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Deployment Guide</h3>
                    <p class="text-gray-600 mb-6">
                        Production deployment strategies with Docker, Kubernetes, and cloud platforms.
                    </p>
                    <ul class="text-sm text-gray-600 space-y-2 mb-6">
                        <li><i class="fas fa-arrow-right text-teal-500 mr-2"></i>Docker deployment</li>
                        <li><i class="fas fa-arrow-right text-teal-500 mr-2"></i>Kubernetes setup</li>
                        <li><i class="fas fa-arrow-right text-teal-500 mr-2"></i>CI/CD pipelines</li>
                    </ul>
                    <a href="#" class="text-teal-600 font-semibold hover:text-teal-700">
                        Deploy Now <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <div class="mt-12 text-center" data-aos="fade-up">
                <div class="bg-gray-50 rounded-xl p-8 inline-block">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Need Help?</h3>
                    <p class="text-gray-600 mb-6">
                        Check out our comprehensive documentation or reach out for support.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="/api/documentation"
                            class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition-colors">
                            <i class="fas fa-book mr-2"></i>
                            Full Documentation
                        </a>
                        <a href="#"
                            class="border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            <i class="fas fa-life-ring mr-2"></i>
                            Get Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-2xl font-bold gradient-text mb-4">Taxly</h3>
                    <p class="text-gray-400">
                        Modern tax management system with FIRS integration for seamless compliance.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Features</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#features" class="hover:text-white transition-colors">FIRS Integration</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors">Automated Invoicing</a>
                        </li>
                        <li><a href="#features" class="hover:text-white transition-colors">Multi-tenancy</a></li>
                        <li><a href="#features" class="hover:text-white transition-colors">API & Webhooks</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Documentation</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Getting Started</a></li>
                        <li><a href="/swagger" class="hover:text-white transition-colors">API Reference</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Deployment Guide</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Best Practices</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Contact Support</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Status Page</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Community</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2024 Taxly. All rights reserved. Built with Laravel & Livewire.</p>
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

        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });

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
                    // Close mobile menu if open
                    mobileMenu.classList.add('hidden');
                }
            });
        });

        // Add scroll effect to navigation
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('bg-white/95');
                nav.classList.remove('bg-white/90');
            } else {
                nav.classList.add('bg-white/90');
                nav.classList.remove('bg-white/95');
            }
        });
    </script>
</body>

</html>
