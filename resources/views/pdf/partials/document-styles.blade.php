<style>
    @page {
        margin: 28px 30px 60px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #19384d;
    }

    .document-shell {
        width: 100%;
    }

    .doc-header {
        width: 100%;
        border-bottom: 2px solid #0b6f81;
        padding-bottom: 14px;
        margin-bottom: 18px;
    }

    .doc-header td {
        vertical-align: top;
    }

    .doc-logo {
        width: 108px;
    }

    .doc-kicker {
        text-transform: uppercase;
        letter-spacing: 1.8px;
        font-size: 9px;
        font-weight: 700;
        color: #0b6f81;
    }

    .doc-title {
        font-size: 22px;
        font-weight: 800;
        color: #0d2438;
        margin: 4px 0 6px;
    }

    .doc-subtitle {
        color: #5f778b;
        line-height: 1.45;
    }

    .doc-stamp {
        text-align: right;
        color: #5a7388;
        font-size: 10px;
        line-height: 1.55;
    }

    .summary-table,
    .meta-table,
    .data-table,
    .photo-grid {
        width: 100%;
        border-collapse: collapse;
    }

    .summary-table {
        margin-bottom: 18px;
    }

    .summary-card {
        width: 25%;
        border: 1px solid #d7e4ec;
        background: #f6fbfd;
        border-radius: 14px;
        padding: 12px;
    }

    .summary-label {
        display: block;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #6c8497;
        margin-bottom: 5px;
    }

    .summary-value {
        display: block;
        font-size: 17px;
        font-weight: 800;
        color: #0d2438;
    }

    .section-title {
        font-size: 13px;
        font-weight: 800;
        color: #0d2438;
        margin: 0 0 10px;
    }

    .section-block {
        margin-bottom: 18px;
    }

    .meta-table td {
        border: 1px solid #dbe7ee;
        padding: 9px 10px;
        vertical-align: top;
    }

    .meta-label {
        display: block;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 1.1px;
        color: #6f8799;
        margin-bottom: 3px;
    }

    .meta-value {
        font-weight: 700;
        color: #12344a;
    }

    .note-box {
        border: 1px solid #dbe7ee;
        background: #fbfdff;
        border-radius: 14px;
        padding: 12px;
        line-height: 1.55;
        color: #28465b;
    }

    .data-table thead th {
        background: #0d2438;
        color: #ffffff;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        text-align: left;
        padding: 10px;
    }

    .data-table tbody td {
        border-bottom: 1px solid #e3edf3;
        padding: 10px;
        vertical-align: top;
    }

    .data-table tbody tr:nth-child(even) td {
        background: #f7fbfd;
    }

    .muted {
        color: #6d8497;
        font-size: 10px;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 999px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .5px;
    }

    .status-success {
        background: #e8f7ee;
        color: #18794e;
    }

    .status-warning {
        background: #fff5d9;
        color: #946200;
    }

    .status-danger {
        background: #fde9e9;
        color: #b42318;
    }

    .status-info {
        background: #e8f5fa;
        color: #0b6f81;
    }

    .photo-grid td {
        width: 50%;
        padding: 6px;
        vertical-align: top;
    }

    .photo-card {
        border: 1px solid #dbe7ee;
        background: #fbfdff;
        border-radius: 14px;
        padding: 8px;
    }

    .photo-card img {
        width: 100%;
        height: 190px;
        object-fit: cover;
        border-radius: 10px;
        margin-bottom: 6px;
    }

    .empty-state {
        border: 1px dashed #cddde7;
        background: #fbfdff;
        border-radius: 14px;
        padding: 14px;
        color: #6a8396;
    }

    .doc-footer {
        position: fixed;
        bottom: -34px;
        left: 0;
        right: 0;
        border-top: 1px solid #d8e5ed;
        padding-top: 8px;
        color: #6a8396;
        font-size: 9px;
    }

    .text-right {
        text-align: right;
    }
</style>
