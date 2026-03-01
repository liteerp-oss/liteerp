@extends('home.layout')

@section('content')

<div class="container-fluid p-0">

    <!-- Hero Section -->
    <section class="bg-dark text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">LiteERP</h1>
            <p class="lead mt-3">
                Structured. Disciplined. Flexible.
            </p>
            <p class="mt-3">
                A lightweight ERP system built for SMEs that want control,
                transparency, and scalable operations.
            </p>
            <div class="mt-4">
                <a href="#features" class="btn btn-primary btn-lg me-2">Explore Features</a>
                <a href="#pricing" class="btn btn-outline-light btn-lg">Get Started</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">Built for Growing Businesses</h2>
            <p class="text-muted col-lg-8 mx-auto">
                LiteERP is designed for small and medium businesses that need
                more than just invoicing. It provides structured workflows,
                permission control, and data integrity without unnecessary complexity.
            </p>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-light py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Core Features</h2>
                <p class="text-muted">Control your operations with discipline and flexibility.</p>
            </div>

            <div class="row g-4">

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold">Flexible Permission System</h5>
                            <p class="text-muted">
                                Create custom groups and assign granular permissions.
                                No rigid roles. You define your structure.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold">Multi-Step Workflow</h5>
                            <p class="text-muted">
                                Sales and purchase processes follow clear approval flows.
                                No skipped steps. No unauthorized approvals.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold">Data Integrity by Default</h5>
                            <p class="text-muted">
                                Approved documents cannot be modified.
                                Ensures consistency, accuracy, and accountability.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold">Transparent Audit Logs</h5>
                            <p class="text-muted">
                                Every action is recorded. Know who created,
                                edited, and approved each document.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold">Scalable Architecture</h5>
                            <p class="text-muted">
                                Works for a single operator or a team of 100+ users.
                                Scale without redesigning your system.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="fw-bold">Stable & Extendable Core</h5>
                            <p class="text-muted">
                                Strong default rules with optional extensions
                                for custom business requirements.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Workflow Section -->
    <section class="py-5">
        <div class="container text-center">
            <h2 class="fw-bold mb-4">How LiteERP Works</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="p-4 border rounded bg-white shadow-sm">
                        <p class="mb-2"><strong>Sales Workflow:</strong></p>
                        <p class="text-muted">
                            Create Order → Approve → Invoice → Approve Invoice → Export Warehouse
                        </p>
                        <hr>
                        <p class="mb-2"><strong>Purchase Workflow:</strong></p>
                        <p class="text-muted">
                            Create Purchase → Approve → Invoice → Receive Goods
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="pricing" class="bg-primary text-white py-5">
        <div class="container text-center">
            <h2 class="fw-bold">Ready to Take Control?</h2>
            <p class="mt-3">
                Start with a structured system today and scale as your business grows.
            </p>
            <a href="#" class="btn btn-light btn-lg mt-3">Launch LiteERP</a>
        </div>
    </section>
</div>

@endsection