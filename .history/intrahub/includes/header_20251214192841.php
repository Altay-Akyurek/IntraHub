<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - IntraHub' : 'IntraHub' ?></title>
    <link rel="stylesheet" href="/php/company_Chat/intrahub/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    /* ===== FORCE DARK COMPLAINT MODAL ===== */

    #complaintModal {
    background: rgba(0,0,0,0.55) !important;
    }

    /* Kart */
    #complaintModal .glass-card {
    background-color: rgba(18, 18, 28, 0.95) !important;
    backdrop-filter: blur(16px) !important;
    border: 1px solid rgba(255,255,255,0.15) !important;
    border-radius: 18px !important;
    color: #e5e7eb !important;
    }

    /* TEXTAREA - BEYAZI ÖLDÜRÜR */
    #complaintModal textarea,
    #complaintModal textarea.form-control {
    background-color: rgba(0,0,0,0.55) !important;
    color: #e5e7eb !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    box-shadow: none !important;
    outline: none !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    }

    /* Placeholder */
    #complaintModal textarea::placeholder {
    color: rgba(255,255,255,0.45) !important;
    }

    /* Focus */
    #complaintModal textarea:focus {
    background-color: rgba(0,0,0,0.65) !important;
    border-color: #6366f1 !important;
    }

    /* Checkbox */
    #complaintModal input[type="checkbox"] {
    background-color: rgba(255,255,255,0.15) !important;
    border: 1px solid rgba(255,255,255,0.3) !important;
    }

    #complaintModal input[type="checkbox"]:checked {
    background-color: #6366f1 !important;
    border-color: #6366f1 !important;
    }

    /* Label */
    #complaintModal label {
    color: #9ca3af !important;
    }


</body>
<canvas id="particle-canvas"></canvas>