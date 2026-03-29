<style>
    :root {
        --vip-gold: #c9a050;
        --vip-blue: #0E1030;

        /* ✅ une seule source de vérité */
        --topbar-h: 86px;
    }

    /* ---------------------------------------------------------
     BASE
  --------------------------------------------------------- */
    body {
        padding-top: var(--topbar-h);
        background-color: #f4f7f6;
        font-family: 'Montserrat', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
    }

    /* ---------------------------------------------------------
     TOP BAR
  --------------------------------------------------------- */
    .vip-navbar {
        height: var(--topbar-h);
        background-color: #ffffff;
        border-bottom: 3px solid var(--vip-gold);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        z-index: 1000;
        display: flex;
        align-items: center;
        padding: 10px 0;
    }

    .vip-navbar .container {
        height: 100%;
        display: flex;
        align-items: center;
    }

    .navbar-brand {
        background: var(--vip-blue);
        border: 2px solid var(--vip-gold);
        padding: 8px 14px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.10);
        text-decoration: none;
    }

    .navbar-brand:hover {
        background: #1a1d4d;
    }

    .navbar-brand img {
        height: 46px;
        width: auto;
        display: block;
    }

    .advisor-box {
        border-left: 1px solid #eee;
        padding-left: 18px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-end;
        line-height: 1.2;
    }

    .advisor-label {
        font-size: 0.7rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 2px;
    }

    .advisor-name {
        font-weight: 700;
        color: var(--vip-blue);
        font-size: 1rem;
    }

    .advisor-phone {
        color: var(--vip-gold);
        font-weight: 700;
        text-decoration: none;
        font-size: 0.95rem;
    }

    .advisor-phone:hover {
        color: var(--vip-blue);
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        :root {
            --topbar-h: 82px;
        }

        .navbar-brand {
            padding: 6px 10px;
            border-radius: 10px;
        }

        .navbar-brand img {
            height: 38px;
        }

        .advisor-box {
            padding-left: 10px;
        }

        .advisor-name {
            font-size: 0.85rem;
        }

        .advisor-phone {
            font-size: 0.8rem;
        }
    }

    /* ---------------------------------------------------------
     CHAT LAYOUT
  --------------------------------------------------------- */
    .chat-wrapper {
        height: calc(100vh - var(--topbar-h));
        display: flex;
        flex-direction: column;
    }

    .chat-container {
        flex: 1 1 auto;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        max-width: 720px;
        width: 100%;
        margin: 0 auto;
        padding: 18px 18px 24px;
    }

    /* ---------------------------------------------------------
     MESSAGES (Agent / User)
  --------------------------------------------------------- */
    .messages__item {
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
        animation: fadeIn 0.25s ease-out;
    }

    .messages__wrapper {
        display: flex;
        align-items: flex-end;
        width: 100%;
    }

    .agent-avatar__icon img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 12px;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .12);
        object-fit: cover;
        background: #fff;
    }

    .agent-msg {
        background: #ffffff;
        padding: 12px 16px;
        border-radius: 18px;
        border-bottom-left-radius: 6px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .06);
        max-width: 78%;
        color: #1b1b1b;
        line-height: 1.45;
    }

    .user-message {
        background: var(--vip-blue);
        color: #fff;
        padding: 10px 16px;
        border-radius: 18px;
        border-bottom-right-radius: 6px;
        align-self: flex-end;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-top: 6px;
        cursor: pointer;
        transition: transform .15s ease, background .15s ease;
    }

    .user-message:hover {
        background: #1a1d50;
        transform: translateY(-1px);
    }

    .edit-badge {
        font-size: 0.72rem;
        background: rgba(255, 255, 255, .18);
        padding: 2px 6px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: .4px;
    }

    /* ---------------------------------------------------------
     RESPONSE AREA (sticky bottom)
  --------------------------------------------------------- */
    .response-area {
        position: sticky;
        bottom: 0;
        z-index: 50;
        background: #f4f7f6;
        border-top: 1px solid rgba(0, 0, 0, .06);
        padding: 12px;
        padding-bottom: calc(12px + env(safe-area-inset-bottom));
    }

    .response-container {
        max-width: 720px;
        margin: 0 auto;
    }

    /* petits ajustements inputs */
    .response-area .form-control,
    .response-area .form-select {
        border-radius: 14px;
    }

    .response-area .btn-primary {
        background: var(--vip-blue);
        border-color: var(--vip-blue);
        border-radius: 14px;
    }

    .response-area .btn-primary:hover {
        background: var(--vip-gold);
        border-color: var(--vip-gold);
    }

    /* ---------------------------------------------------------
     ANIMATION
  --------------------------------------------------------- */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>