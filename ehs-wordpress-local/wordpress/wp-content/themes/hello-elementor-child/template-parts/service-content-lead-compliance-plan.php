<?php
/**
 * Lead Compliance Plan Services – full page content (PHP template)
 *
 * Used when single-services.php detects slug `lead-compliance-plan-services`.
 * Spec: LEAD_COMPLIANCE_PLAN_ENHANCED_V2.json
 *
 * @package HelloElementorChild
 */

if (!defined('ABSPATH')) {
    exit;
}

$caltrans_url = ehs_get_service_url('caltrans-construction-safety-services');
$ih_url = ehs_get_service_url('industrial-hygiene-san-diego');
$contact_url = ehs_get_page_url('contact');
$phone = function_exists('ehs_get_option') ? ehs_get_option('phone') : '(619) 288-3094';
$phone_link = function_exists('ehs_get_phone') ? ehs_get_phone(true) : '6192883094';
?>

<div class="service-section" id="lead-compliance-plan-services-overview">
    <h2>Lead Compliance Plan Services</h2>
    <p>EHS Analytical Solutions provides comprehensive lead compliance services for California construction projects. Our Certified Industrial Hygienists (CIH) ensure full compliance with Cal/OSHA Title 8 Section 1532.1 and related regulations.</p>
    <p>A Lead Compliance Plan is Cal/OSHA's comprehensive written program required whenever construction activities may disturb lead-containing materials. For Caltrans projects specifically, Section 7-1.02K9 of the Special Provisions mandates a detailed Lead Compliance Plan for all work involving lead-containing coatings on bridges and structures. The plan ensures worker protection through exposure assessment, engineering controls, respiratory protection, medical surveillance, and Work Area Monitoring throughout the project lifecycle.</p>
    <div class="service-callout" style="background: var(--ehs-accent-light, #f0f7f0); padding: 1rem 1.25rem; border-left: 4px solid var(--ehs-primary, #2d5a27); margin: 1rem 0;">
        <strong>100+ Caltrans Lead Compliance Plans | All 12 Districts | DVBE #2017031 | CIH Certified</strong>
    </div>
</div>

<div class="service-section" id="when-you-need-a-lead-compliance-plan">
    <h2>When You Need a Lead Compliance Plan</h2>
    <p>Lead compliance planning is required for:</p>
    <ul class="service-list-content">
        <li><strong>Caltrans bridge maintenance and painting projects</strong> – All Caltrans projects involving lead-containing coatings require Section 7-1.02K9 compliance.</li>
        <li><strong>Renovation of pre-1978 structures</strong> – Buildings and infrastructure constructed before lead paint was banned.</li>
        <li><strong>Demolition activities</strong> – Any demolition that may disturb lead paint, especially in older industrial facilities.</li>
        <li><strong>Industrial facility maintenance</strong> – Steel structure maintenance, tank painting, and equipment refurbishment.</li>
        <li><strong>Steel structure painting and surface prep</strong> – Abrasive blasting, power tool cleaning, and manual surface preparation.</li>
        <li><strong>Any construction activity disturbing lead paint or coatings</strong> – Work that creates airborne lead dust or fumes.</li>
        <li><strong>EPA RRP (Renovation, Repair, and Painting) projects</strong> – Pre-1978 residential and child-occupied facilities.</li>
        <li><strong>Projects where exposure exceeds Cal/OSHA action levels</strong> – 30 µg/m³ 8-hour time-weighted average.</li>
        <li><strong>Federal facility projects with lead-containing materials</strong> – USACE, Navy, and other federal construction.</li>
    </ul>
</div>

<div class="service-section" id="california-lead-in-construction-requirements">
    <h2>California Lead in Construction Requirements</h2>
    <p>Cal/OSHA requires a written Lead Compliance Plan for construction work that may expose workers to lead. Structures built before 1978 often contain lead-based paint. Our lead compliance services include:</p>

    <h3>Lead Compliance Plan Development</h3>
    <ul>
        <li><strong>Written Lead Compliance Plan</strong> per Cal/OSHA 1532.1 and Caltrans Section 7-1.02K9 – Comprehensive program addressing all regulatory requirements.</li>
        <li><strong>Initial exposure assessment and air monitoring strategy</strong> – Determining worker exposure levels and monitoring frequency.</li>
        <li><strong>Engineering controls and work practice specifications</strong> – Defining containment, ventilation, and exposure reduction methods.</li>
        <li><strong>Competent Person training and designation</strong> – Ensuring qualified oversight per Cal/OSHA requirements.</li>
    </ul>

    <h3>Work Area Monitoring (WAM) – Caltrans Required</h3>
    <ul>
        <li><strong>Personal breathing zone air monitoring</strong> – Measuring actual worker exposure to airborne lead during operations.</li>
        <li><strong>Area air monitoring for containment verification</strong> – Ensuring engineering controls are effective.</li>
        <li><strong>Real-time monitoring results and exposure tracking</strong> – Immediate notification of exposure levels.</li>
        <li><strong>Caltrans submittal and reporting</strong> – Complying with district-specific documentation requirements.</li>
    </ul>

    <h3>Worker Protection Programs</h3>
    <ul>
        <li><strong>Respiratory protection program implementation</strong> – Selection, fit-testing, and training per Cal/OSHA 5144.</li>
        <li><strong>Personal protective equipment (PPE) selection and training</strong> – Appropriate protection for exposure levels.</li>
        <li><strong>Medical surveillance program coordination</strong> – Baseline and periodic testing for exposed workers.</li>
        <li><strong>Worker training on lead hazards and safe work practices</strong> – Required initial and annual refresher training.</li>
    </ul>

    <h3>Compliance Documentation &amp; Support</h3>
    <ul>
        <li><strong>Environmental controls and containment strategies</strong> – HEPA filtration, wet methods, and exposure reduction.</li>
        <li><strong>Post-abatement clearance testing</strong> – Verification that work areas are safe for re-occupancy.</li>
        <li><strong>Cal/OSHA inspection support and regulatory defense</strong> – Assistance during agency inspections.</li>
        <li><strong>Ongoing compliance monitoring throughout project duration</strong> – Ensuring continued adherence to LCP requirements.</li>
    </ul>
</div>


<div class="service-section" id="caltrans-lead-compliance-plans-completed">
    <h2>100+ Caltrans Lead Compliance Plans Completed</h2>
    <p>EHS Analytical Solutions is California's most experienced provider of Lead Compliance Plans for Caltrans highway and bridge construction. We have successfully completed over 100 Lead Compliance Plans across all 12 Caltrans districts, making us the trusted choice for contractors bidding on Caltrans projects with lead-containing coatings work.</p>
    <p>Our deep understanding of Caltrans Special Provisions Section 7-1.02K9 ensures your project meets all requirements from bid through project closeout. We know what each district expects, how to coordinate with Resident Engineers, and how to navigate the approval process efficiently. This expertise helps contractors avoid bid mistakes, submittal rejections, and costly project delays.</p>
    <h3>Caltrans-Specific Expertise Includes:</h3>
    <ul>
        <li>Special Provisions Section 7-1.02K9 Lead Compliance Plan requirements</li>
        <li>Work Area Monitoring protocols for bridge rehabilitation and painting</li>
        <li>Coordination with Caltrans Resident Engineers and district offices</li>
        <li>Integration with Caltrans traffic control and public safety requirements</li>
        <li>District-specific submittal formats and approval processes</li>
        <li>Caltrans Standard Specifications Section 7 (Safety Requirements) compliance</li>
    </ul>
</div>

<div class="service-section" id="all-12-caltrans-districts-served">
    <h2>All 12 Caltrans Districts Served</h2>
    <p>We provide Lead Compliance Plan services throughout California with successful project completions in all 12 Caltrans districts:</p>
    <div class="service-section-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; margin: 1rem 0;">
        <div><strong>District 1 – Eureka</strong><br>Del Norte, Humboldt, Lake, Mendocino</div>
        <div><strong>District 2 – Redding</strong><br>Lassen, Modoc, Plumas, Shasta, Siskiyou, Tehama, Trinity</div>
        <div><strong>District 3 – Marysville</strong><br>Butte, Colusa, El Dorado, Glenn, Nevada, Placer, Sacramento, Sierra, Sutter, Yolo, Yuba</div>
        <div><strong>District 4 – Oakland</strong><br>Alameda, Contra Costa, Marin, Napa, San Francisco, San Mateo, Santa Clara, Solano, Sonoma</div>
        <div><strong>District 5 – San Luis Obispo</strong><br>Monterey, San Benito, San Luis Obispo, Santa Barbara, Santa Cruz</div>
        <div><strong>District 6 – Fresno</strong><br>Fresno, Kings, Kern, Madera, Tulare</div>
        <div><strong>District 7 – Los Angeles</strong><br>Los Angeles, Ventura</div>
        <div><strong>District 8 – San Bernardino</strong><br>Riverside, San Bernardino</div>
        <div><strong>District 9 – Bishop</strong><br>Inyo, Mono</div>
        <div><strong>District 10 – Stockton</strong><br>Alpine, Amador, Calaveras, Mariposa, Merced, San Joaquin, Stanislaus, Tuolumne</div>
        <div><strong>District 11 – San Diego</strong><br>Imperial, San Diego</div>
        <div><strong>District 12 – Irvine</strong><br>Orange</div>
    </div>
    <p>From major bridge rehabilitation projects in urban districts to rural highway work, our experience spans the full range of Caltrans construction environments and project types.</p>
</div>

<div class="service-section" id="our-lead-compliance-plan-development-process">
    <h2>Our Lead Compliance Plan Development Process</h2>
    <p>We streamline the Lead Compliance Plan development process to meet your bid deadlines and project schedules:</p>
    <ol class="service-process-list" style="list-style: none; padding-left: 0;">
        <li style="margin-bottom: 1.25rem;"><strong>Step 1 – Project Review &amp; Assessment (2–3 days):</strong> We review Caltrans Special Provisions, conduct site assessment if needed, and determine lead presence and concentration. For bidding support, we can provide preliminary LCP cost estimates and scope.</li>
        <li style="margin-bottom: 1.25rem;"><strong>Step 2 – Lead Compliance Plan Development (1–2 weeks):</strong> Our CIH team develops a comprehensive written program addressing all Cal/OSHA 1532.1 and Section 7-1.02K9 requirements, including exposure assessment, engineering controls, respiratory protection, medical surveillance, and Work Area Monitoring protocols.</li>
        <li style="margin-bottom: 1.25rem;"><strong>Step 3 – Submittal &amp; Caltrans Approval (1–2 weeks):</strong> We prepare the submittal package per district requirements, submit to the Resident Engineer, and respond to any review comments to obtain approval prior to work commencement.</li>
        <li style="margin-bottom: 1.25rem;"><strong>Step 4 – Training &amp; Implementation (1 week):</strong> We provide Competent Person training, deliver worker safety training, and support plan implementation at project startup including initial Work Area Monitoring.</li>
        <li style="margin-bottom: 1.25rem;"><strong>Step 5 – Ongoing Monitoring &amp; Support (project duration):</strong> Throughout the project, we conduct periodic Work Area Monitoring, verify continued compliance, provide technical support, and complete all required Caltrans reporting and documentation.</li>
    </ol>
    <p><strong>Total development time from start to approved plan is typically 3–5 weeks.</strong> For urgent bid situations, we can expedite development with advance notice.</p>
</div>

<div class="service-section" id="benefits-of-our-lead-compliance-services">
    <h2>Benefits of Our Lead Compliance Services</h2>
    <p>Why Contractors Choose EHS Analytical Solutions</p>
    <div class="service-section-3col" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin: 1.5rem 0;">
        <div class="service-col">
            <h3>100+ Plans Completed</h3>
            <p>We've developed and implemented over 100 Lead Compliance Plans for Caltrans bridge projects across all 12 California districts. This experience with Section 7-1.02K9 requirements ensures your project avoids submittal rejections, approval delays, and costly change orders.</p>
        </div>
        <div class="service-col">
            <h3>Certified Industrial Hygienists (CIH)</h3>
            <p>Our team holds the highest professional certification in industrial hygiene (CIH #9695CP). This expertise in exposure assessment, air monitoring, and Cal/OSHA compliance goes far beyond basic safety training and ensures technically sound, defensible Lead Compliance Plans.</p>
        </div>
        <div class="service-col">
            <h3>DVBE Certification Helps Your Bid</h3>
            <p>As a Disabled Veteran Business Enterprise (DVBE #2017031), we help Caltrans prime contractors meet small business participation goals. This means our subcontract counts toward your DVBE requirements, potentially earning you a 5% bid preference while getting expert CIH-level services.</p>
        </div>
    </div>
    <h3>Additional Benefits</h3>
    <ul>
        <li><strong>Ensure Cal/OSHA and EPA compliance</strong> – Our comprehensive plans address all Title 8 CCR 1532.1 requirements and EPA RRP regulations, protecting you from citations, fines, and stop-work orders that can cost tens of thousands in delays.</li>
        <li><strong>Protect workers from lead exposure</strong> – Proper exposure assessment and control measures keep workers safe and healthy while meeting your legal duty of care and avoiding potential litigation.</li>
        <li><strong>Avoid costly citations and project delays</strong> – Cal/OSHA lead violations carry penalties up to $25,000 per serious violation. Our proactive compliance approach prevents expensive disruptions.</li>
        <li><strong>Professional monitoring and documentation</strong> – CIH-certified Work Area Monitoring provides legally defensible exposure data and demonstrates due diligence to Cal/OSHA, Caltrans, and your insurance carrier.</li>
        <li><strong>Certified Industrial Hygienist oversight</strong> – CIH certification represents advanced expertise that general safety consultants don't have, ensuring your plan meets the technical rigor Caltrans districts expect.</li>
    </ul>
</div>

<div class="service-section service-faq" id="frequently-asked-questions-about-lead-compliance-plans">
    <h2>Frequently Asked Questions About Lead Compliance Plans</h2>
    <div class="service-accordions-container">
        <div class="service-accordion">
            <button class="accordion-header" aria-expanded="false" aria-controls="lcp-faq-1"><span class="accordion-title">How long does it take to develop a Lead Compliance Plan?</span><span class="accordion-icon" aria-hidden="true">+</span></button>
            <div id="lcp-faq-1" class="accordion-content" role="region"><div class="accordion-inner"><p>Development typically takes 2–3 weeks from project kickoff to final submittal-ready document. For contractors preparing Caltrans bids, we can provide preliminary LCP estimates within 2–3 business days. The Caltrans approval process adds 1–2 weeks depending on the district's review schedule and Resident Engineer workload.</p></div></div>
        </div>
        <div class="service-accordion">
            <button class="accordion-header" aria-expanded="false" aria-controls="lcp-faq-2"><span class="accordion-title">What is Work Area Monitoring and when is it required?</span><span class="accordion-icon" aria-hidden="true">+</span></button>
            <div id="lcp-faq-2" class="accordion-content" role="region"><div class="accordion-inner"><p>Work Area Monitoring (WAM) involves personal breathing zone air sampling to measure workers' actual exposure to airborne lead during construction activities. Caltrans Section 7-1.02K9 requires WAM for all lead-containing coatings work on bridges and structures. Monitoring frequency depends on exposure levels but typically occurs weekly during active lead work.</p></div></div>
        </div>
        <div class="service-accordion">
            <button class="accordion-header" aria-expanded="false" aria-controls="lcp-faq-3"><span class="accordion-title">How does DVBE certification benefit our Caltrans bid?</span><span class="accordion-icon" aria-hidden="true">+</span></button>
            <div id="lcp-faq-3" class="accordion-content" role="region"><div class="accordion-inner"><p>Caltrans awards a 5% bid preference to contractors meeting DVBE participation goals. Our DVBE certification (#2017031) means your subcontract with us counts toward small business requirements, making your bid more competitive. You get CIH-level technical expertise while earning DVBE credit.</p></div></div>
        </div>
        <div class="service-accordion">
            <button class="accordion-header" aria-expanded="false" aria-controls="lcp-faq-4"><span class="accordion-title">Do you provide the Competent Person for our project?</span><span class="accordion-icon" aria-hidden="true">+</span></button>
            <div id="lcp-faq-4" class="accordion-content" role="region"><div class="accordion-inner"><p>Yes. We can provide a CIH-certified Competent Person for your lead work, or we can train and designate one of your staff members. Cal/OSHA requires a Competent Person with specific training in lead hazards, exposure recognition, and control measures on all projects involving lead-containing materials.</p></div></div>
        </div>
        <div class="service-accordion">
            <button class="accordion-header" aria-expanded="false" aria-controls="lcp-faq-5"><span class="accordion-title">What if lead exposure exceeds Cal/OSHA limits during work?</span><span class="accordion-icon" aria-hidden="true">+</span></button>
            <div id="lcp-faq-5" class="accordion-content" role="region"><div class="accordion-inner"><p>If Work Area Monitoring reveals exposures above the Permissible Exposure Limit (50 µg/m³), we immediately notify you and implement additional controls. This may include enhanced engineering controls, modified work practices, upgraded respiratory protection, or all three. We update the Lead Compliance Plan as needed and provide follow-up monitoring to verify effectiveness.</p></div></div>
        </div>
        <div class="service-accordion">
            <button class="accordion-header" aria-expanded="false" aria-controls="lcp-faq-6"><span class="accordion-title">Can you support multiple Caltrans projects simultaneously?</span><span class="accordion-icon" aria-hidden="true">+</span></button>
            <div id="lcp-faq-6" class="accordion-content" role="region"><div class="accordion-inner"><p>Yes. We maintain a team of CIH professionals and can support multiple concurrent projects across different districts. Each project receives dedicated CIH oversight with assigned project managers and responsive local support throughout California.</p></div></div>
        </div>
    </div>
</div>

<div class="service-section" id="our-lead-compliance-credentials">
    <h2>Our Lead Compliance Credentials</h2>
    <p><strong>Professional Certifications:</strong> Certified Industrial Hygienist (CIH #9695CP) | Certified Safety Professional (CSP) | Cal/OSHA Lead Competent Person | EPA Lead-Safe Certified Renovator</p>
    <p><strong>Business Certifications:</strong> DVBE #2017031 (California Department of General Services) | SDVOSB (U.S. Small Business Administration) | Veteran-Owned Business</p>
    <p><strong>Experience:</strong> 100+ Lead Compliance Plans completed across all 12 Caltrans districts | 15+ years of lead exposure assessment and monitoring | Expert testimony and regulatory defense | Cal/OSHA consultation program partner</p>
</div>

<div class="service-section">
    <p>Looking for comprehensive Caltrans construction safety services beyond lead compliance? See our complete <a href="<?php echo esc_url($caltrans_url); ?>">Caltrans Construction Safety Services →</a></p>
    <p>Explore our full range of <a href="<?php echo esc_url($ih_url); ?>">Industrial Hygiene Services →</a></p>
</div>

<section class="service-cta" id="ready-to-get-started">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        <h2>Ready to Get Started?</h2>
        <p>Whether you're bidding a Caltrans bridge project or planning renovation work with lead-containing materials, we can help. Contact us today for a project quote and Lead Compliance Plan proposal. As a DVBE-certified firm, we help you meet small business participation goals while ensuring full Cal/OSHA and Caltrans Section 7-1.02K9 compliance.</p>
        <div class="service-cta-buttons">
            <a href="<?php echo esc_url($contact_url); ?>" class="ehs-btn ehs-btn-solid-secondary ehs-btn-lg">Request Quote</a>
            <a href="tel:<?php echo esc_attr($phone_link); ?>" class="ehs-btn ehs-btn-outline-white ehs-btn-lg"><?php echo esc_html($phone ?: 'Call (619) 288-3094'); ?></a>
            <a href="<?php echo esc_url($caltrans_url); ?>" class="ehs-btn ehs-btn-outline-white ehs-btn-lg">View Caltrans Services</a>
        </div>
        <p style="margin-top: 1rem;">Phone: <?php echo esc_html($phone ?: '(619) 288-3094'); ?> | Email: adam@ehsanalytical.com | Serving all 12 Caltrans districts from San Diego</p>
    </div>
</section>
