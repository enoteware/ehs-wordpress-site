<?php
/**
 * Template Name: Home Page (New Design)
 * Description: SDVOSB/DVBE Federal & Caltrans Positioning
 *
 * @package HelloElementorChild
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<!-- ========================================
     HERO SECTION
     ======================================== -->
<section class="ehs-hero-section" style="background-image: url('<?php echo get_stylesheet_directory_uri(); ?>/assets/images/hero-background.jpg');">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>SDVOSB-Certified Environmental Health & Safety Solutions</h1>
        <p class="hero-subheadline" style="color: var(--ehs-gold);">Federal Military Construction | Caltrans Projects | Industrial&nbsp;Hygiene</p>

        <div class="hero-badges" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap; margin-top: 30px; margin-bottom: 30px;">
            <span class="hero-badge" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 18px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; backdrop-filter: blur(10px); display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.3);"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>SDVOSB Certified</span>
            <span class="hero-badge" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 18px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; backdrop-filter: blur(10px); display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.3);"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>DVBE #2017031</span>
            <span class="hero-badge" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 18px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; backdrop-filter: blur(10px); display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.3);"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>CIH &amp; CSP</span>
            <span class="hero-badge" style="background: rgba(255,255,255,0.2); color: white; padding: 10px 18px; border-radius: 6px; font-size: 0.9rem; font-weight: 600; backdrop-filter: blur(10px); display: inline-flex; align-items: center; gap: 8px; border: 1px solid rgba(255,255,255,0.3);"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#4ade80" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>EM 385-1-1 Experts</span>
        </div>

        <div class="hero-cta-group" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/ssho-services-california/" class="ehs-btn ehs-btn-solid-green ehs-btn-lg">Federal Contractors - <br>Get SSHO&nbsp;Quote</a>
            <a href="/caltrans-construction-safety-services/" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">Caltrans Bid&nbsp;Support</a>
            <a href="tel:6192883094" class="ehs-btn ehs-btn-outline-white ehs-btn-lg">Call (619) 288-3094</a>
        </div>

        <p class="hero-trust-signals">20+ Years Experience • 500+ Projects Completed • SDVOSB&nbsp;Certified</p>
    </div>
</section>

<!-- ========================================
     SERVICES OVERVIEW SECTION
     ======================================== -->
<section class="ehs-services-section">
    <div class="container">
        <h2>Specialized Safety & Environmental Services</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            SDVOSB and DVBE certified services for federal military construction, Caltrans projects, and industrial facilities throughout California and&nbsp;nationwide.
        </p>

        <!-- FEATURED SERVICES - Large Cards -->
        <div class="featured-services-grid">

            <!-- Featured Service #1: SSHO Services -->
            <a href="https://ehs-local.ddev.site/ssho-services-california/" class="service-card service-card--featured" style="background-image: url('https://ehs-local.ddev.site/wp-content/uploads/2026/01/construction-safety-site-supervisor-4160237.jpg'); background-size: cover; background-position: center;">
                <div class="service-card__content">
                    <div class="service-card__badge"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>SDVOSB Certified</div>
                    <h3 class="service-card__title">SSHO Services - Federal Military Construction</h3>
                    <p class="service-card__excerpt">SDVOSB-certified Site Safety and Health Officers for USACE, NAVFAC, and DoD construction projects with full EM&nbsp;385-1-1&nbsp;compliance.</p>
                    <ul class="service-card__highlights">
                        <li>EM 385-1-1 compliance experts</li>
                        <li>SDVOSB helps meet small business goals</li>
                        <li>CIH & CSP certified professionals</li>
                        <li>California & nationwide coverage</li>
                    </ul>
                    <span class="service-card__link">Learn More About SSHO&nbsp;Services&nbsp;&rarr;</span>
                </div>
            </a>

            <!-- Featured Service #2: Construction Safety Consulting -->
            <a href="https://ehs-local.ddev.site/construction-safety-consulting/" class="service-card service-card--featured" style="background-image: url('https://ehs-local.ddev.site/wp-content/uploads/2026/01/pexels-photo-8293680.jpeg'); background-size: cover; background-position: center;">
                <div class="service-card__content">
                    <h3 class="service-card__title">Construction Safety Consulting</h3>
                    <p class="service-card__excerpt">Dedicated on-site safety professionals for construction projects. Safety program development, daily inspections, training, and Cal/OSHA compliance&nbsp;expertise.</p>
                    <ul class="service-card__highlights">
                        <li>On-site safety officers</li>
                        <li>Safety program development</li>
                        <li>Cal/OSHA compliance</li>
                        <li>Federal & state projects</li>
                    </ul>
                    <span class="service-card__link">View Construction Safety&nbsp;Services&nbsp;&rarr;</span>
                </div>
            </a>

            <!-- Featured Service #3: Caltrans Construction Safety Services -->
            <a href="https://ehs-local.ddev.site/caltrans-construction-safety-services/" class="service-card service-card--featured" style="background-image: url('https://ehs-local.ddev.site/wp-content/uploads/2026/01/highway-construction-safety-caltrans-35300832.jpg'); background-size: cover; background-position: center;">
                <div class="service-card__content">
                    <div class="service-card__badge"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>DVBE #2017031</div>
                    <h3 class="service-card__title">Caltrans Construction Safety Services</h3>
                    <p class="service-card__excerpt">DVBE-certified safety services for Caltrans highway and bridge construction across all 12 California&nbsp;districts.</p>
                    <ul class="service-card__highlights">
                        <li>All 12 Caltrans districts served</li>
                        <li>DVBE #2017031 certification</li>
                        <li>Lead Compliance Plan experts</li>
                        <li>Bridge rehabilitation specialists</li>
                    </ul>
                    <span class="service-card__link">Learn More About Caltrans&nbsp;Services&nbsp;&rarr;</span>
                </div>
            </a>

            <!-- Featured Service #4: Federal Contracting Services -->
            <a href="https://ehs-local.ddev.site/federal-contracting-sdvosb/" class="service-card service-card--featured" style="background-image: url('https://ehs-local.ddev.site/wp-content/uploads/2026/01/government-building-federal-contract-18738059.jpg'); background-size: cover; background-position: center;">
                <div class="service-card__content">
                    <div class="service-card__badge"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>SDVOSB Certified</div>
                    <h3 class="service-card__title">Federal Contracting Services</h3>
                    <p class="service-card__excerpt">SDVOSB-certified environmental health and safety services for federal contractors on USACE, NAVFAC, and DoD projects&nbsp;nationwide.</p>
                    <ul class="service-card__highlights">
                        <li>SDVOSB certification verified</li>
                        <li>Meet subcontracting goals</li>
                        <li>All federal agencies</li>
                        <li>Nationwide coverage</li>
                    </ul>
                    <span class="service-card__link">View Federal Contracting&nbsp;Services&nbsp;&rarr;</span>
                </div>
            </a>

            <!-- Featured Service #5: Industrial Hygiene Services -->
            <a href="https://ehs-local.ddev.site/industrial-hygiene-san-diego/" class="service-card service-card--featured" style="background-image: url('https://ehs-local.ddev.site/wp-content/uploads/2026/01/pexels-9242209-Workplace-safety-Confident-worker-adjusts-safety-g.jpg'); background-size: cover; background-position: center;">
                <div class="service-card__content">
                    <div class="service-card__badge"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>CIH Certified</div>
                    <h3 class="service-card__title">Industrial Hygiene Services</h3>
                    <p class="service-card__excerpt">CIH-certified industrial hygiene assessments, exposure monitoring, air quality testing, and compliance programs for industrial facilities&nbsp;nationwide.</p>
                    <ul class="service-card__highlights">
                        <li>CIH certified professionals</li>
                        <li>Exposure assessments</li>
                        <li>Air quality monitoring</li>
                        <li>Compliance programs</li>
                    </ul>
                    <span class="service-card__link">View Industrial Hygiene&nbsp;Services&nbsp;&rarr;</span>
                </div>
            </a>

            <!-- Featured Service #6: Lead Compliance Plan Services -->
            <a href="https://ehs-local.ddev.site/lead-compliance-plan-services/" class="service-card service-card--featured" style="background-image: url('https://ehs-local.ddev.site/wp-content/uploads/2026/01/lead-paint-testing-inspection-31101763.jpg'); background-size: cover; background-position: center;">
                <div class="service-card__content">
                    <div class="service-card__badge"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px; vertical-align: -2px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>100+ Plans Completed</div>
                    <h3 class="service-card__title">Lead Compliance Plan Services</h3>
                    <p class="service-card__excerpt">Comprehensive Lead Compliance Plans for Caltrans bridge projects and construction involving lead-containing paint&nbsp;removal.</p>
                    <ul class="service-card__highlights">
                        <li>100+ plans completed</li>
                        <li>Caltrans approved</li>
                        <li>Cal/OSHA Section 1532.1 compliant</li>
                        <li>CIH certified oversight</li>
                    </ul>
                    <span class="service-card__link">Learn More About Lead&nbsp;Compliance&nbsp;&rarr;</span>
                </div>
            </a>

        </div>

        <!-- ADDITIONAL SERVICES - Smaller Cards -->
        <h3 style="margin-top: 80px; margin-bottom: 40px; text-align: center; font-size: 1.8rem;">Additional Environmental & Safety Services</h3>

        <div class="service-related__grid">

            <!-- EHS Consulting -->
            <a href="https://ehs-local.ddev.site/environmental-health-and-safety-ehs-consulting/" class="service-card">
                <div class="service-card__icon">
                    <img src="https://ehs-local.ddev.site/wp-content/uploads/2026/01/pexels-photo-5368685.jpeg"
                         alt="EHS Consulting icon"
                         loading="lazy">
                </div>
                <div class="service-card__content">
                    <h3 class="service-card__title">Environmental Health & Safety Consulting</h3>
                    <p class="service-card__excerpt">Comprehensive EHS program development, compliance audits, and strategic safety management for California businesses.</p>
                    <span class="service-card__link">Learn More &rarr;</span>
                </div>
            </a>

            <!-- Environmental Testing Services (Combined) -->
            <a href="https://ehs-local.ddev.site/mold-testing/" class="service-card">
                <div class="service-card__icon">
                    <img src="https://ehs-local.ddev.site/wp-content/uploads/2026/01/extinguishing-a-forest-fire-1024x1024-1.jpg"
                         alt="Environmental Testing icon"
                         loading="lazy">
                </div>
                <div class="service-card__content">
                    <h3 class="service-card__title">Environmental Testing Services</h3>
                    <p class="service-card__excerpt">Mold testing, asbestos testing, indoor air quality assessments, water damage evaluations, and fire/smoke damage assessments.</p>
                    <span class="service-card__link">Learn More &rarr;</span>
                </div>
            </a>

        </div>
    </div>
</section>

<!-- ========================================
     FEDERAL AGENCIES SECTION
     ======================================== -->
<section class="federal-agencies-section" style="background: var(--ehs-light-gray); padding: 80px 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 20px;">Trusted by Federal Agencies & Prime Contractors</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            As an SDVOSB, we help prime contractors meet small business subcontracting goals on federal military construction projects while providing expert EM 385-1-1 compliant safety&nbsp;services.
        </p>

        <div class="agency-logos-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 40px; align-items: center; margin-bottom: 40px;">

            <div class="agency-logo" style="text-align: center;">
                <div style="background: var(--ehs-navy); color: white; padding: 40px 20px; border-radius: 8px; font-weight: 700; font-size: 1.2rem;">USACE</div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: var(--ehs-dark-gray);">U.S. Army Corps of Engineers</p>
            </div>

            <div class="agency-logo" style="text-align: center;">
                <div style="background: var(--ehs-navy); color: white; padding: 40px 20px; border-radius: 8px; font-weight: 700; font-size: 1.2rem;">NAVFAC</div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: var(--ehs-dark-gray);">Naval Facilities Engineering</p>
            </div>

            <div class="agency-logo" style="text-align: center;">
                <div style="background: var(--ehs-navy); color: white; padding: 40px 20px; border-radius: 8px; font-weight: 700; font-size: 1.2rem;">VA</div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: var(--ehs-dark-gray);">Department of Veterans Affairs</p>
            </div>

            <div class="agency-logo" style="text-align: center;">
                <div style="background: var(--ehs-navy); color: white; padding: 40px 20px; border-radius: 8px; font-weight: 700; font-size: 1.2rem;">AIR FORCE</div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: var(--ehs-dark-gray);">U.S. Air Force</p>
            </div>

            <div class="agency-logo" style="text-align: center;">
                <div style="background: var(--ehs-navy); color: white; padding: 40px 20px; border-radius: 8px; font-weight: 700; font-size: 1.2rem;">DoD</div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: var(--ehs-dark-gray);">Department of Defense</p>
            </div>

            <div class="agency-logo" style="text-align: center;">
                <div style="background: var(--ehs-navy); color: white; padding: 40px 20px; border-radius: 8px; font-weight: 700; font-size: 1.2rem;">CALTRANS</div>
                <p style="margin-top: 10px; font-size: 0.85rem; color: var(--ehs-dark-gray);">California DOT</p>
            </div>

        </div>

        <div style="text-align: center;">
            <a href="/federal-contracting-sdvosb/" class="ehs-btn ehs-btn-outline">Learn More About Our Federal Contracting&nbsp;Capabilities&nbsp;&rarr;</a>
        </div>
    </div>
</section>

<!-- ========================================
     WHY CHOOSE US SECTION
     ======================================== -->
<section class="why-choose-us-section" style="padding: 80px 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 20px;">Why Federal Contractors & Caltrans Primes Choose EHS Analytical</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            Our SDVOSB and DVBE certifications help you meet small business participation goals while our certified professionals ensure project&nbsp;compliance.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">

            <!-- Column 1: SDVOSB & DVBE Certified -->
            <div class="why-column" style="text-align: center; padding: 30px; background: var(--ehs-light-gray); border-radius: 8px;">
                <div style="font-size: 3rem; color: var(--ehs-green); margin-bottom: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11.46 20.846a12 12 0 0 1 -7.96 -14.846a12 12 0 0 0 8.5 -3a12 12 0 0 0 8.5 3a12 12 0 0 1 -.09 7.06" />
                        <path d="M15 19l2 2l4 -4" />
                    </svg>
                </div>
                <h3 style="color: var(--ehs-navy); margin-bottom: 15px;">SDVOSB & DVBE Certified</h3>
                <p>Help meet your small business subcontracting goals on federal and California state projects. Our SDVOSB status counts toward your participation requirements.</p>
                <ul style="list-style: none; padding: 0; margin-top: 20px; text-align: left;">
                    <li style="margin-bottom: 10px;">✓ SDVOSB verified</li>
                    <li style="margin-bottom: 10px;">✓ DVBE #2017031</li>
                    <li style="margin-bottom: 10px;">✓ Meet subcontracting goals</li>
                    <li style="margin-bottom: 10px;">✓ Veteran-owned leadership</li>
                </ul>
            </div>

            <!-- Column 2: EM 385-1-1 & Caltrans Experts -->
            <div class="why-column" style="text-align: center; padding: 30px; background: var(--ehs-light-gray); border-radius: 8px;">
                <div style="font-size: 3rem; color: var(--ehs-green); margin-bottom: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 15l8.385 -8.415a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385" />
                        <path d="M3 7v4a1 1 0 0 0 1 1h4" />
                        <path d="M7 7a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" />
                    </svg>
                </div>
                <h3 style="color: var(--ehs-navy); margin-bottom: 15px;">EM 385-1-1 & Caltrans Experts</h3>
                <p>Our CIH and CSP certified professionals are thoroughly trained in EM 385-1-1 requirements, Caltrans specifications, and Cal/OSHA regulations.</p>
                <ul style="list-style: none; padding: 0; margin-top: 20px; text-align: left;">
                    <li style="margin-bottom: 10px;">✓ EM 385-1-1 trained SSHOs</li>
                    <li style="margin-bottom: 10px;">✓ 100+ Lead Compliance Plans</li>
                    <li style="margin-bottom: 10px;">✓ CIH & CSP certified</li>
                    <li style="margin-bottom: 10px;">✓ Caltrans approved</li>
                </ul>
            </div>

            <!-- Column 3: California & Nationwide Coverage -->
            <div class="why-column" style="text-align: center; padding: 30px; background: var(--ehs-light-gray); border-radius: 8px;">
                <div style="font-size: 3rem; color: var(--ehs-green); margin-bottom: 20px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                </div>
                <h3 style="color: var(--ehs-navy); margin-bottom: 15px;">California & Nationwide</h3>
                <p>Based in San Diego with experience across all 12 Caltrans districts and military installations throughout California and the United States.</p>
                <ul style="list-style: none; padding: 0; margin-top: 20px; text-align: left;">
                    <li style="margin-bottom: 10px;">✓ All 12 Caltrans districts</li>
                    <li style="margin-bottom: 10px;">✓ California military bases</li>
                    <li style="margin-bottom: 10px;">✓ Federal projects nationwide</li>
                    <li style="margin-bottom: 10px;">✓ 24-hour quote response</li>
                </ul>
            </div>

        </div>
    </div>
</section>

<!-- ========================================
     MILITARY INSTALLATIONS SECTION
     ======================================== -->
<section class="military-installations-section" style="background: var(--ehs-navy); color: white; padding: 80px 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 20px; color: white;">Military Installations We Serve</h2>
        <p style="text-align: center; font-size: 1.1rem; max-width: 800px; margin: 0 auto 60px; color: rgba(255,255,255,0.9);">
            Our SDVOSB-certified safety professionals have extensive experience supporting construction projects at military installations throughout California and&nbsp;nationwide.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px;">

            <!-- San Diego Area -->
            <div class="installation-region">
                <h3 style="color: var(--ehs-green); margin-bottom: 20px; border-bottom: 2px solid var(--ehs-green); padding-bottom: 10px;">San Diego Area</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">Naval Base San Diego</li>
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">MCAS Miramar</li>
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">NAS North Island</li>
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">MCRD San Diego</li>
                    <li style="padding: 8px 0;">Camp Pendleton</li>
                </ul>
            </div>

            <!-- Southern California -->
            <div class="installation-region">
                <h3 style="color: var(--ehs-green); margin-bottom: 20px; border-bottom: 2px solid var(--ehs-green); padding-bottom: 10px;">Southern California</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">Edwards Air Force Base</li>
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">China Lake</li>
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">Twentynine Palms</li>
                    <li style="padding: 8px 0;">Point Mugu</li>
                </ul>
            </div>

            <!-- Central & Northern California -->
            <div class="installation-region">
                <h3 style="color: var(--ehs-green); margin-bottom: 20px; border-bottom: 2px solid var(--ehs-green); padding-bottom: 10px;">Central & Northern California</h3>
                <ul style="list-style: none; padding: 0;">
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">Vandenberg Space Force Base</li>
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">Travis Air Force Base</li>
                    <li style="padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.1);">Beale Air Force Base</li>
                    <li style="padding: 8px 0;">NPS Monterey</li>
                </ul>
            </div>

        </div>

        <div style="text-align: center; margin-top: 60px;">
            <a href="/ssho-services-california/" class="ehs-btn ehs-btn-solid-green ehs-btn-lg">View Our Complete SSHO&nbsp;Services&nbsp;&rarr;</a>
        </div>
    </div>
</section>

<!-- ========================================
     CALTRANS DISTRICTS SECTION
     ======================================== -->
<section class="caltrans-districts-section" style="padding: 80px 0; background: var(--ehs-light-gray);">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 20px;">Caltrans Safety Services - All 12 Districts</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            DVBE-certified safety services for Caltrans highway and bridge construction projects throughout California. We provide Safety Representatives, Lead Compliance Plans, and Work Area Monitoring across all&nbsp;districts.
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; max-width: 1000px; margin: 0 auto;">

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 1</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Eureka</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 2</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Redding</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 3</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Marysville</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 4</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Oakland</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 5</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">San Luis Obispo</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 6</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Fresno</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 7</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Los Angeles</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 8</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">San Bernardino</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 9</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Bishop</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 10</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Stockton</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 11</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">San Diego</p>
            </div>

            <div class="district-card" style="background: white; padding: 20px; border-radius: 8px; border-left: 4px solid var(--ehs-green);">
                <strong style="color: var(--ehs-navy); font-size: 1.2rem;">District 12</strong>
                <p style="margin: 5px 0; color: var(--ehs-dark-gray);">Irvine</p>
            </div>

        </div>

        <div style="text-align: center; margin-top: 60px;">
            <a href="/caltrans-construction-safety-services/" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">View Our Complete Caltrans&nbsp;Services&nbsp;&rarr;</a>
        </div>
    </div>
</section>

<!-- ========================================
     TRUST & CREDENTIALS SECTION
     ======================================== -->
<section class="ehs-credentials-section">
    <div class="credentials-grid">
        <div class="credentials-content">
            <h2>Trusted by California's Leading Organizations</h2>
            <p>Veteran-owned with industry-leading certifications and proven expertise in environmental health and safety consulting.</p>

            <div class="metrics-row">
                <div class="metric">
                    <span class="metric-value">20+</span>
                    <span class="metric-label">Years Experience</span>
                </div>
                <div class="metric">
                    <span class="metric-value">500+</span>
                    <span class="metric-label">Projects Completed</span>
                </div>
                <div class="metric">
                    <span class="metric-value">SDVOSB</span>
                    <span class="metric-label">Certified</span>
                </div>
            </div>
        </div>

        <div class="credentials-badges">
            <?php ehs_render_certification_badges(); ?>
        </div>
    </div>
</section>

<!-- ========================================
     ABOUT SECTION
     ======================================== -->
<section class="ehs-about-section" style="background: linear-gradient(135deg, #003366 0%, #004080 100%);">
    <div class="about-overlay"></div>
    <div class="about-content">
        <h2>Who We Are</h2>

        <p>
            EHS Analytical is a California-based environmental health and safety consulting firm dedicated to protecting
            people, projects, and the environment. We provide comprehensive EHS solutions tailored to the unique needs
            of California businesses, construction projects, and federal&nbsp;agencies.
        </p>

        <p>
            As a Service-Disabled Veteran-Owned Small Business (SDVOSB), we bring military discipline, precision, and
            commitment to every project. Founded by veterans of the United States Marine Corps and Navy, our team
            understands the importance of rigorous safety standards and unwavering&nbsp;compliance.
        </p>

        <p>
            Our certified professionals hold industry-leading credentials including CIH, CSP, CHST, and PMP certifications.
            With over 20 years of experience and 500+ successful projects, we've established ourselves as trusted advisors
            to California's construction, manufacturing, healthcare, and government&nbsp;sectors.
        </p>

        <!-- Stats Grid -->
        <div class="about-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; margin-top: 40px; padding: 40px; background: rgba(255,255,255,0.1); border-radius: 8px;">
            <div class="stat" style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--ehs-green);">100+</div>
                <div style="font-size: 1rem; margin-top: 10px;">Lead Compliance Plans&nbsp;Completed</div>
            </div>
            <div class="stat" style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--ehs-green);">12</div>
                <div style="font-size: 1rem; margin-top: 10px;">Caltrans Districts&nbsp;Served</div>
            </div>
            <div class="stat" style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--ehs-green);">15+</div>
                <div style="font-size: 1rem; margin-top: 10px;">Years Federal&nbsp;Projects</div>
            </div>
            <div class="stat" style="text-align: center;">
                <div style="font-size: 2.5rem; font-weight: 700; color: var(--ehs-green);">20+</div>
                <div style="font-size: 1rem; margin-top: 10px;">Military&nbsp;Installations</div>
            </div>
        </div>

        <div style="margin-top: 40px;">
            <a href="/about" class="ehs-btn ehs-btn-solid-secondary">Meet Our&nbsp;Team</a>
        </div>
    </div>
</section>

<!-- ========================================
     LATEST RESOURCES SECTION
     ======================================== -->
<section class="ehs-resources-section">
    <div class="container">
        <h2>Latest Insights & Resources</h2>
        <p style="text-align: center; font-size: 1.1rem; color: var(--ehs-dark-gray); max-width: 800px; margin: 0 auto 60px;">
            Stay informed with our latest articles, industry updates, and EHS best practices.
        </p>

        <div class="article-related__grid">
            <?php
            $latest_posts = ehs_get_latest_posts(3);

            if (!empty($latest_posts)) {
                foreach ($latest_posts as $post) {
                    ehs_homepage_render_article_card($post);
                }
            } else {
                // Fallback if no posts exist
                echo '<p style="text-align: center; color: var(--ehs-dark-gray);">Check back soon for the latest updates and insights from our EHS experts.</p>';
            }
            ?>
        </div>

        <div style="text-align: center; margin-top: 40px;">
            <a href="/blog" class="ehs-btn ehs-btn-outline">View All&nbsp;Articles</a>
        </div>
    </div>
</section>

<!-- ========================================
     FINAL CTA SECTION
     ======================================== -->
<section class="service-cta" style="background: var(--ehs-navy); color: white; text-align: center; padding: 80px 20px;">
    <div class="container">
        <h2 style="color: white; margin-bottom: 20px;">Ready to Include Us in Your Next Federal or Caltrans Bid?</h2>
        <p style="font-size: 1.1rem; margin-bottom: 20px; max-width: 800px; margin-left: auto; margin-right: auto;">
            Whether you're bidding on USACE military construction, Caltrans highway projects, or need industrial hygiene support, our SDVOSB/DVBE certifications help you meet small business participation goals while providing expert safety&nbsp;services.
        </p>
        <p style="margin-top: 20px; font-size: 1rem; color: rgba(255,255,255,0.9); margin-bottom: 40px;">
            ⏱️ We respond to all federal and Caltrans bid inquiries within 24 hours.
        </p>
        <div class="service-cta-buttons" style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
            <a href="/contact/" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">Request a&nbsp;Quote</a>
            <a href="tel:6192883094" class="ehs-btn ehs-btn-outline-white ehs-btn-lg">
                <span style="font-size: 1.2rem; font-weight: 700;">(619) 288-3094</span>
            </a>
        </div>
    </div>
</section>

<?php
get_footer();
