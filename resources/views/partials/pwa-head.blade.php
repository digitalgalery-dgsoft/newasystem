<!-- PWA Web App Manifest & Metadata -->
<link rel="manifest" href="/manifest.json">
<meta name="theme-color" content="#0F52BA">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="ASystem">
<meta name="application-name" content="ASystem - Support System ESA Groups">
<meta name="msapplication-TileColor" content="#0F52BA">
<meta name="msapplication-TileImage" content="/icons/icon-192x192.png">
<meta name="msapplication-starturl" content="/">
<meta name="msapplication-navbutton-color" content="#0F52BA">

<!-- Favicons & App Icons -->
<link rel="icon" type="image/svg+xml" href="/icons/icon.svg">
<link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">

<!-- Service Worker Registration -->
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js', { scope: '/' })
                .then(function(reg) {
                    // Cek pembaruan service worker
                    reg.onupdatefound = function() {
                        var installingWorker = reg.installing;
                        installingWorker.onstatechange = function() {
                            if (installingWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                console.log('[PWA] Versi baru ASystem tersedia. Memperbarui cache...');
                            }
                        };
                    };
                })
                .catch(function(err) {
                    console.warn('[PWA] ServiceWorker registration warning:', err);
                });
        });
    }
</script>
