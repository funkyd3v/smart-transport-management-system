<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; }
    html { scroll-behavior: smooth; }

    body {
        font-family: 'Inter', sans-serif;
        background: #F8F9FB;
        color: #1a1f2e;
        overflow-x: hidden;
    }

    h1, h2, h3, h4, h5, h6 { font-family: 'Roboto', sans-serif; }

    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-track { background: #f1f5f9; }
    ::-webkit-scrollbar-thumb { background: #F59E0B; border-radius: 10px; }

    .text-gradient {
        background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .btn-primary {
        background: linear-gradient(135deg, #F59E0B, #D97706);
        color: #fff;
        font-weight: 600;
        border-radius: 14px;
        padding: 13px 28px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.35);
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-primary::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.25), transparent);
        transition: left 0.5s ease;
    }

    .btn-primary:hover::after { left: 100%; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(245, 158, 11, 0.45); }

    .btn-outline {
        background: transparent;
        color: #1a1f2e;
        font-weight: 600;
        border-radius: 14px;
        padding: 12px 28px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 15px;
        border: 1.5px solid #E2E8F0;
        transition: all 0.25s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-outline:hover { border-color: #F59E0B; color: #D97706; background: #FFFBEB; transform: translateY(-2px); }

    #navbar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 100;
        padding: 18px 40px;
        transition: all 0.3s ease;
        background: transparent;
    }

    #navbar.scrolled {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(20px);
        box-shadow: 0 1px 0 #E2E8F0, 0 4px 24px rgba(0, 0, 0, 0.06);
        padding: 12px 40px;
    }

    .nav-inner { max-width: 1280px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
    .nav-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; }
    .nav-logo-icon { width: 38px; height: 38px; border-radius: 12px; background: linear-gradient(135deg, #F59E0B, #D97706); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35); }
    .nav-logo-text { font-family: 'Roboto', sans-serif; font-weight: 800; font-size: 20px; color: #0F172A; }
    .nav-links { display: flex; align-items: center; gap: 36px; }
    .nav-links a { text-decoration: none; color: #475569; font-size: 14px; font-weight: 500; transition: color 0.2s; }
    .nav-links a:hover { color: #F59E0B; }
    .nav-ctas { display: flex; align-items: center; gap: 12px; }
    .nav-signin { font-size: 14px; font-weight: 600; color: #475569; text-decoration: none; padding: 8px 16px; border-radius: 10px; transition: all 0.2s; }
    .nav-signin:hover { color: #F59E0B; background: #FEF3C7; }
    .nav-demo { font-size: 14px; font-weight: 700; background: linear-gradient(135deg, #F59E0B, #D97706); color: #fff; text-decoration: none; padding: 9px 20px; border-radius: 10px; box-shadow: 0 3px 12px rgba(245, 158, 11, 0.3); transition: all 0.25s; }
    .nav-demo:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4); }
    .hamburger { display: none; background: none; border: none; cursor: pointer; padding: 6px; }

    #mobile-menu { display: none; background: #fff; border-radius: 16px; margin-top: 12px; padding: 20px 24px; box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12); border: 1px solid #E2E8F0; }
    #mobile-menu.open { display: block; }
    #mobile-menu a { display: block; padding: 10px 0; color: #475569; text-decoration: none; font-weight: 500; border-bottom: 1px solid #F1F5F9; transition: color 0.2s; }
    #mobile-menu a:hover { color: #F59E0B; }
    .mobile-ctas { display: flex; gap: 10px; margin-top: 16px; }
    .mobile-ctas a { flex: 1; text-align: center; font-size: 14px; font-weight: 600; border-radius: 10px; padding: 10px; text-decoration: none; }
    .m-signin { border: 1.5px solid #E2E8F0; color: #475569; }
    .m-demo { background: linear-gradient(135deg, #F59E0B, #D97706); color: #fff; }

    @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-14px); } }
    @keyframes float2 { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.6; transform: scale(0.9); } }
    @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

    .hero { min-height: 100vh; display: flex; align-items: center; padding: 120px 40px 80px; position: relative; overflow: hidden; }
    .hero-dot-bg { position: absolute; inset: 0; background-image: radial-gradient(circle, #CBD5E1 1px, transparent 1px); background-size: 32px 32px; opacity: 0.5; }
    .hero-glow { position: absolute; inset: 0; background: radial-gradient(ellipse 70% 55% at 65% 5%, rgba(245, 158, 11, 0.09) 0%, transparent 65%), radial-gradient(ellipse 40% 35% at 5% 85%, rgba(96, 165, 250, 0.05) 0%, transparent 60%); }
    .hero-inner { max-width: 1280px; margin: 0 auto; position: relative; z-index: 2; display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
    .hero-badge { display: inline-flex; align-items: center; gap: 8px; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 100px; padding: 6px 16px; margin-bottom: 24px; }
    .hero-badge-dot { width: 7px; height: 7px; border-radius: 50%; background: #F59E0B; animation: pulse 2s ease infinite; }
    .hero-badge span { font-size: 13px; font-weight: 600; color: #B45309; }
    .hero-h1 { font-family: 'Roboto', sans-serif; font-weight: 800; font-size: 62px; line-height: 1.07; color: #0F172A; margin-bottom: 22px; letter-spacing: -1.5px; }
    .hero-sub { font-size: 18px; color: #64748B; line-height: 1.7; margin-bottom: 36px; max-width: 480px; }
    .hero-ctas { display: flex; gap: 14px; margin-bottom: 52px; flex-wrap: wrap; }
    .hero-stats { display: flex; gap: 28px; flex-wrap: wrap; }
    .hero-stat { text-align: center; }
    .hero-stat-num { font-family: 'Roboto', sans-serif; font-weight: 800; font-size: 28px; color: #F59E0B; }
    .hero-stat-label { font-size: 12px; color: #94A3B8; margin-top: 2px; font-weight: 500; }
    .stat-sep { width: 1px; height: 42px; background: #E2E8F0; align-self: center; }
    .float-card { position: absolute; background: #fff; border-radius: 14px; padding: 14px 18px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.10); border: 1px solid #F1F5F9; white-space: nowrap; }
    .float-card.f1 { animation: float 5s ease-in-out infinite; }
    .float-card.f2 { animation: float2 5.5s ease-in-out 1.5s infinite; }
    .dash-card { background: #fff; border-radius: 24px; box-shadow: 0 24px 64px rgba(0, 0, 0, 0.10), 0 2px 8px rgba(0, 0, 0, 0.04); border: 1px solid #F1F5F9; overflow: hidden; position: relative; z-index: 1; }
    .dash-topbar { padding: 14px 20px; background: #F8F9FB; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; gap: 8px; }
    .dot-btn { width: 10px; height: 10px; border-radius: 50%; }
    .dash-body { padding: 18px 20px; }
    .kpi-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 9px; margin-bottom: 16px; }
    .kpi { background: #F8F9FB; border-radius: 11px; padding: 11px 13px; border: 1px solid #F1F5F9; transition: all 0.2s; cursor: default; }
    .kpi:hover { border-color: #FDE68A; background: #FFFBEB; }
    .kpi-label { font-size: 10px; color: #94A3B8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; margin-bottom: 5px; display: flex; align-items: center; gap: 5px; }
    .kpi-val { font-family: 'Roboto', sans-serif; font-weight: 800; font-size: 19px; color: #0F172A; }
    .kpi-sub { font-size: 10px; font-weight: 600; margin-top: 3px; }
    .trip-row { display: flex; align-items: center; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid #F8F9FB; }
    .trip-row:last-child { border: none; }
    .trip-icon { width: 32px; height: 32px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: 12px; flex-shrink: 0; }
    .trip-name { font-size: 12px; font-weight: 600; color: #0F172A; }
    .trip-client { font-size: 10px; color: #94A3B8; margin-top: 1px; }
    .trip-amount { font-size: 13px; font-weight: 700; color: #0F172A; }
    .trip-badge { font-size: 10px; font-weight: 600; padding: 2px 8px; border-radius: 20px; }
    .b-green { background: #DCFCE7; color: #15803D; }
    .b-amber { background: #FEF3C7; color: #B45309; }
    .b-blue { background: #DBEAFE; color: #1D4ED8; }

    .section-label { display: inline-flex; align-items: center; gap: 8px; background: #FEF3C7; border: 1px solid #FDE68A; border-radius: 100px; padding: 5px 14px; margin-bottom: 18px; }
    .section-label span { font-size: 12px; font-weight: 700; color: #B45309; text-transform: uppercase; letter-spacing: 0.6px; }
    .section-title { font-family: 'Roboto', sans-serif; font-weight: 800; font-size: 44px; color: #0F172A; line-height: 1.1; letter-spacing: -1px; }
    .section-sub { font-size: 17px; color: #64748B; line-height: 1.7; max-width: 560px; }

    .modules-section {
        position: relative;
        overflow: hidden;
        background:
            radial-gradient(circle at 18% 18%, rgba(20, 184, 166, 0.16) 0%, transparent 28%),
            radial-gradient(circle at 82% 16%, rgba(245, 158, 11, 0.12) 0%, transparent 26%),
            radial-gradient(circle at 75% 78%, rgba(56, 189, 248, 0.12) 0%, transparent 24%),
            linear-gradient(135deg, #F8FBFF 0%, #EEF6FF 42%, #ECFDF5 100%);
    }

    .modules-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: radial-gradient(circle, rgba(148, 163, 184, 0.26) 1px, transparent 1px);
        background-size: 34px 34px;
        opacity: 0.28;
        pointer-events: none;
    }

    .modules-sky {
        position: absolute;
        inset: 0;
        pointer-events: none;
        overflow: hidden;
    }

    .modules-glow {
        position: absolute;
        border-radius: 999px;
        filter: blur(8px);
        opacity: 0.85;
    }

    .modules-glow-a {
        top: 8%;
        left: 8%;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(20, 184, 166, 0.18), transparent 70%);
    }

    .modules-glow-b {
        right: 7%;
        bottom: 12%;
        width: 260px;
        height: 260px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.14), transparent 70%);
    }

    .star {
        position: absolute;
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 0 14px rgba(255, 255, 255, 0.65);
        animation: twinkle 4.5s ease-in-out infinite;
    }

    .star-1 { top: 16%; left: 14%; }
    .star-2 { top: 22%; right: 18%; animation-delay: 1.2s; }
    .star-3 { top: 62%; left: 8%; animation-delay: 2.1s; }
    .star-4 { top: 70%; right: 9%; animation-delay: 0.7s; }
    .star-5 { top: 42%; right: 32%; animation-delay: 2.8s; }

    .shooting-star {
        position: absolute;
        height: 2px;
        width: 160px;
        border-radius: 999px;
        transform: rotate(-18deg);
        transform-origin: left center;
        animation: shooting-star 8s linear infinite;
        opacity: 0;
    }

    .shooting-star::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 50%;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        transform: translateY(-50%);
        box-shadow: 0 0 18px currentColor;
        background: currentColor;
    }

    .shooting-star-1 {
        top: 18%;
        left: -15%;
        color: rgba(245, 158, 11, 0.9);
        background: linear-gradient(90deg, rgba(245, 158, 11, 0), rgba(245, 158, 11, 0.95));
        animation-delay: 0.4s;
    }

    .shooting-star-2 {
        top: 36%;
        left: -18%;
        color: rgba(20, 184, 166, 0.9);
        background: linear-gradient(90deg, rgba(20, 184, 166, 0), rgba(20, 184, 166, 0.92));
        animation-delay: 3s;
    }

    .shooting-star-3 {
        top: 68%;
        left: -20%;
        color: rgba(96, 165, 250, 0.88);
        background: linear-gradient(90deg, rgba(96, 165, 250, 0), rgba(96, 165, 250, 0.9));
        animation-delay: 5.5s;
    }

    .module-card { background: #fff; border-radius: 20px; padding: 28px; border: 1.5px solid #F1F5F9; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: default; }
    .module-card:hover { border-color: #FDE68A; box-shadow: 0 12px 40px rgba(245, 158, 11, 0.12); transform: translateY(-6px); }
    .module-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; font-size: 20px; }
    .module-title { font-family: 'Roboto', sans-serif; font-weight: 700; font-size: 18px; color: #0F172A; margin-bottom: 10px; }
    .module-desc { font-size: 14px; color: #64748B; line-height: 1.65; margin-bottom: 18px; }
    .module-tags { display: flex; flex-wrap: wrap; gap: 7px; }
    .module-tag { font-size: 11px; font-weight: 600; color: #64748B; background: #F1F5F9; border-radius: 20px; padding: 4px 11px; }

    .flow-step { display: flex; gap: 22px; align-items: flex-start; padding-bottom: 34px; position: relative; }
    .flow-step:not(:last-child)::before { content: ''; position: absolute; left: 23px; top: 56px; bottom: 0; width: 2px; background: linear-gradient(180deg, #FDE68A, #F1F5F9); }
    .flow-num { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-family: 'Roboto', sans-serif; font-weight: 800; font-size: 18px; flex-shrink: 0; position: relative; z-index: 1; }
    .f-active { background: linear-gradient(135deg, #F59E0B, #D97706); color: #fff; box-shadow: 0 6px 20px rgba(245, 158, 11, 0.35); }
    .f-idle { background: #F1F5F9; color: #94A3B8; }
    .flow-title { font-family: 'Roboto', sans-serif; font-weight: 700; font-size: 19px; color: #0F172A; margin-bottom: 6px; padding-top: 10px; }
    .flow-desc { font-size: 14px; color: #64748B; line-height: 1.65; }

    .benefit-card { background: #fff; border-radius: 20px; padding: 28px; border: 1.5px solid #F1F5F9; text-align: center; transition: all 0.3s; cursor: default; }
    .benefit-card:hover { border-color: #FDE68A; box-shadow: 0 8px 28px rgba(245, 158, 11, 0.10); transform: translateY(-4px); }
    .benefit-icon { width: 56px; height: 56px; border-radius: 16px; background: #FEF3C7; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; font-size: 22px; color: #F59E0B; }

    .cta-banner { background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%); border-radius: 28px; padding: 72px 56px; text-align: center; position: relative; overflow: hidden; }
    .cta-banner::before { content: ''; position: absolute; top: -60px; right: -60px; width: 280px; height: 280px; border-radius: 50%; background: radial-gradient(circle, rgba(245, 158, 11, 0.2), transparent 70%); }
    .cta-banner::after { content: ''; position: absolute; bottom: -70px; left: -50px; width: 240px; height: 240px; border-radius: 50%; background: radial-gradient(circle, rgba(96, 165, 250, 0.1), transparent 70%); }
    .cta-banner h2 { font-family: 'Roboto', sans-serif; font-weight: 800; font-size: 40px; color: #fff; margin-bottom: 16px; letter-spacing: -1px; position: relative; z-index: 2; line-height: 1.1; }
    .cta-banner p { font-size: 17px; color: #94A3B8; margin-bottom: 36px; max-width: 520px; margin-left: auto; margin-right: auto; position: relative; z-index: 2; }
    .cta-btns { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; position: relative; z-index: 2; }
    .btn-white { background: #fff; color: #0F172A; font-weight: 600; border-radius: 14px; padding: 13px 28px; display: inline-flex; align-items: center; gap: 8px; font-size: 15px; transition: all 0.25s; text-decoration: none; box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15); }
    .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2); }
    .btn-ghost { background: rgba(255, 255, 255, 0.08); color: #fff; font-weight: 600; border-radius: 14px; padding: 13px 28px; display: inline-flex; align-items: center; gap: 8px; font-size: 15px; border: 1.5px solid rgba(255, 255, 255, 0.15); transition: all 0.25s; text-decoration: none; }
    .btn-ghost:hover { background: rgba(255, 255, 255, 0.14); transform: translateY(-2px); }

    .contact-card { background: #fff; border-radius: 28px; padding: 48px; border: 1.5px solid #F1F5F9; box-shadow: 0 8px 40px rgba(0, 0, 0, 0.06); }
    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 7px; }
    .form-input { width: 100%; background: #F8F9FB; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 13px 16px; font-size: 15px; color: #0F172A; font-family: 'Inter', sans-serif; outline: none; transition: all 0.2s; }
    .form-input::placeholder { color: #CBD5E1; }
    .form-input:focus { border-color: #F59E0B; background: #FFFBEB; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.12); }
    textarea.form-input { resize: none; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .marquee-track { overflow: hidden; background: #fff; border-top: 1px solid #F1F5F9; border-bottom: 1px solid #F1F5F9; }
    .marquee-inner { display: flex; animation: marquee 28s linear infinite; width: max-content; }
    .marquee-item { display: flex; align-items: center; gap: 12px; padding: 18px 32px; white-space: nowrap; font-size: 13px; font-weight: 600; color: #94A3B8; }
    .marquee-sep { color: #FDE68A; font-size: 14px; }

    .tech-pill { display: flex; align-items: center; gap: 10px; padding: 11px 18px; background: #fff; border: 1.5px solid #F1F5F9; border-radius: 14px; font-size: 14px; font-weight: 600; color: #1a1f2e; transition: all 0.2s; cursor: default; }
    .tech-pill:hover { border-color: #FDE68A; box-shadow: 0 4px 14px rgba(245, 158, 11, 0.10); }
    .tech-pill-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 14px; }

    .footer { background: #0F172A; color: #fff; }
    .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 48px; padding: 64px 40px; max-width: 1280px; margin: 0 auto; }
    .footer-desc { font-size: 14px; color: #b3b6bc; line-height: 1.7; margin: 14px 0 24px; max-width: 320px; }
    .footer-socials { display: flex; gap: 10px; }
    .footer-social { width: 36px; height: 36px; border-radius: 10px; background: rgba(255, 255, 255, 0.06); display: flex; align-items: center; justify-content: center; color: #64748B; font-size: 13px; text-decoration: none; transition: all 0.2s; }
    .footer-social:hover { background: rgba(245, 158, 11, 0.2); color: #F59E0B; }
    .footer-col-title { font-size: 12px; font-weight: 700; color: #fff; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 18px; }
    .footer-links { list-style: none; padding: 0; }
    .footer-links li { margin-bottom: 10px; }
    .footer-links a { font-size: 14px; color: #b3b6bc; text-decoration: none; transition: color 0.2s; }
    .footer-links a:hover { color: #F59E0B; }
    .footer-bottom { border-top: 1px solid rgba(255, 255, 255, 0.06); padding: 20px 40px; max-width: 1280px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
    .footer-bottom span { font-size: 13px; color: #475569; }

    .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.65s ease, transform 0.65s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }

    @keyframes twinkle {
        0%, 100% { opacity: 0.35; transform: scale(0.9); }
        50% { opacity: 1; transform: scale(1.1); }
    }

    @keyframes shooting-star {
        0% { transform: translateX(0) translateY(0) rotate(-18deg); opacity: 0; }
        8% { opacity: 1; }
        18% { opacity: 0.95; }
        28% { opacity: 0; }
        100% { transform: translateX(170vw) translateY(34vh) rotate(-18deg); opacity: 0; }
    }

    @media (max-width: 1024px) {
        .hero-inner { grid-template-columns: 1fr; gap: 48px; }
        .hero-right { display: none; }
        #navbar { padding: 16px 24px; }
        #navbar.scrolled { padding: 12px 24px; }
        .nav-links, .nav-ctas { display: none; }
        .hamburger { display: flex; }
        .section-title { font-size: 34px; }
        .hero-h1 { font-size: 44px; }
        .footer-grid { grid-template-columns: 1fr 1fr; gap: 32px; }
        .workflow-grid { grid-template-columns: 1fr !important; gap: 40px !important; }
        .cta-banner { padding: 48px 28px; }
        .cta-banner h2 { font-size: 30px; }
    }

    @media (max-width: 640px) {
        .hero { padding: 100px 20px 60px; }
        .hero-h1 { font-size: 36px; }
        .hero-ctas { flex-direction: column; }
        .form-row { grid-template-columns: 1fr; }
        .footer-grid { grid-template-columns: 1fr; }
        .contact-card { padding: 28px 20px; }
        .footer-bottom { flex-direction: column; gap: 8px; text-align: center; }
        .hero-stats { gap: 18px; }
        #navbar { padding: 14px 20px; }
    }

    @media (min-width: 1025px) {
        #mobile-menu { display: none !important; }
    }
</style>
