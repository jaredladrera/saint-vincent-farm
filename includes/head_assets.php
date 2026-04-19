<?php
// BASE_URL is defined in index.php — use it for asset paths
$asset_base = defined('BASE_URL') ? BASE_URL : '';
?>
<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet" />
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
<!-- Main Stylesheet -->
<link href="<?= $asset_base ?>/assets/css/main.css" rel="stylesheet" />

<!-- Critical inline styles: keep drawer hidden before CSS loads -->
<style>
  .cart-drawer{position:fixed!important;top:0!important;right:0!important;bottom:0!important;width:340px!important;max-width:95vw!important;background:#fff!important;z-index:1201!important;display:flex!important;flex-direction:column!important;transform:translateX(100%)!important;transition:transform .32s cubic-bezier(.32,.72,0,1)!important;box-shadow:-8px 0 40px rgba(0,0,0,.15)!important;}
  .cart-drawer.open{transform:translateX(0)!important;}
  .cart-backdrop{position:fixed!important;inset:0!important;background:rgba(0,0,0,.45)!important;z-index:1200!important;opacity:0!important;pointer-events:none!important;transition:opacity .3s!important;}
  .cart-backdrop.open{opacity:1!important;pointer-events:all!important;}
  .mobile-menu{display:none!important;}
  .mobile-menu.open{display:block!important;}
  .user-dropdown{position:absolute!important;top:calc(100% + 10px)!important;right:0!important;background:#fff!important;border-radius:14px!important;border:1px solid #e8ede8!important;box-shadow:0 12px 40px rgba(0,0,0,.12)!important;min-width:230px!important;z-index:1050!important;opacity:0!important;transform:translateY(-8px) scale(.97)!important;pointer-events:none!important;transition:all .2s!important;}
  .user-dropdown.open{opacity:1!important;transform:translateY(0) scale(1)!important;pointer-events:all!important;}
  .user-backdrop{position:fixed!important;inset:0!important;z-index:1049!important;display:none!important;}
  .user-backdrop.open{display:block!important;}
</style>
