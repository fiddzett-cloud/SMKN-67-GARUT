from pathlib import Path
import re

root = Path('.')
files = [
    'admin/dashboard.php',
    'admin/daftarfeedback.php',
    'admin/feedback.php',
    'admin/histori-aspirasi.php',
    'admin/progresperbaikan.php',
    'admin/data-siswa.php',
    'admin/profil.php',
    'admin/tambahpengaduan.php',
]

style_block = '''<style>
body {
    background: linear-gradient(135deg, #f8fafc, #fff7ed);
    padding-bottom: 120px;
}

.card {
    border-radius: 16px;
}

.btn-primary {
    background: #f97316;
    border: none;
}
.btn-primary:hover {
    background: #ea580c;
}

.progress-bar {
    background: linear-gradient(90deg, #fb923c, #facc15);
}

/* Header Style */
.top-header {
    background: white;
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    position: sticky;
    top: 0;
    z-index: 40;
}

.top-header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1.5rem;
}

.top-header-logo {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.top-header-logo img {
    height: 45px;
}

.top-header-brand {
    display: flex;
    flex-direction: column;
}

.top-header-brand span {
    font-weight: 700;
    background: linear-gradient(to right, #ea580c, #f97316);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.25rem;
}

.top-header-brand small {
    color: #6b7280;
    font-size: 0.7rem;
    font-weight: 500;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* Bottom Navigation */
.bottom-nav {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-top: 1px solid #e5e7eb;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
    z-index: 40;
}

.bottom-nav-container {
    display: flex;
    justify-content: space-around;
    align-items: center;
    padding: 0.5rem;
    gap: 0.5rem;
}

.bottom-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 0.5rem;
    border-radius: 0.75rem;
    text-decoration: none;
    color: #4b5563;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.2s;
    flex: 1;
    min-height: 80px;
}

.bottom-nav-item i {
    font-size: 1.5rem;
    margin-bottom: 0.25rem;
}

.bottom-nav-item:hover {
    color: #ea580c;
    background: #fff7ed;
}

.bottom-nav-item.active {
    color: white;
    background: linear-gradient(135deg, #f97316, #ea580c);
}

.bottom-nav-item.active:hover {
    color: white;
}
</style>'''

top_header = '''<!-- TOP HEADER -->
<div class="top-header">
    <div class="top-header-content">
        <div class="top-header-logo">
            <img src="../img/logostm.png">
            <div class="top-header-brand">
                <span>STM</span>
                <small>AL MADANI GARUT</small>
            </div>
        </div>
        <a href="../auth/logout.php" class="btn btn-danger btn-sm rounded-pill">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>
'''

base_nav = '''<!-- BOTTOM NAVIGATION -->
<div class="bottom-nav">
    <div class="bottom-nav-container">
        <a href="dashboard.php" class="bottom-nav-item{dashboard_active}">
            <i class="bi bi-house-door-fill"></i>
            <span>Dashboard</span>
        </a>
        <a href="list_aspirasi.php" class="bottom-nav-item{list_active}">
            <i class="bi bi-list-check"></i>
            <span>List</span>
        </a>
        <a href="histori-aspirasi.php" class="bottom-nav-item{histori_active}">
            <i class="bi bi-clock-history"></i>
            <span>Histori</span>
        </a>
        <a href="data-siswa.php" class="bottom-nav-item{siswa_active}">
            <i class="bi bi-people-fill"></i>
            <span>Siswa</span>
        </a>
        <a href="daftarfeedback.php" class="bottom-nav-item{chat_active}">
            <i class="bi bi-chat-dots-fill"></i>
            <span>Chat</span>
        </a>
        <a href="progresperbaikan.php" class="bottom-nav-item{progres_active}">
            <i class="bi bi-graph-up"></i>
            <span>Progres</span>
        </a>
    </div>
</div>
'''

active_map = {
    'dashboard.php': {'dashboard_active': ' active'},
    'list_aspirasi.php': {'list_active': ' active'},
    'histori-aspirasi.php': {'histori_active': ' active'},
    'data-siswa.php': {'siswa_active': ' active'},
    'daftarfeedback.php': {'chat_active': ' active'},
    'progresperbaikan.php': {'progres_active': ' active'},
    'feedback.php': {'chat_active': ' active'},
}

header_re = re.compile(r'(?s)(<!-- (?:HEADER BAR|MINIMALIST HEADER) -->\s*)?<header class="bg-gradient-to-r from-white via-orange-50 to-white shadow-lg border-b border-orange-100 sticky top-0 z-50".*?</header>\s*')
bottom_re = re.compile(r'(?s)(<!-- BOTTOM NAVIGATION -->\s*)?<nav class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t border-gray-200 z-40".*?</nav>\s*')

icon_link = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">'

for fname in files:
    path = root / fname
    if not path.exists():
        print('missing', fname)
        continue
    text = path.read_text(encoding='utf-8')
    if icon_link in text and style_block not in text:
        text = text.replace(icon_link, icon_link + '\n\n' + style_block)
    new_text, n1 = header_re.subn(top_header, text, count=1)
    if n1 > 0:
        text = new_text
    active = active_map.get(Path(fname).name, {})
    nav = base_nav.format(
        dashboard_active=active.get('dashboard_active',''),
        list_active=active.get('list_active',''),
        histori_active=active.get('histori_active',''),
        siswa_active=active.get('siswa_active',''),
        chat_active=active.get('chat_active',''),
        progres_active=active.get('progres_active',''),
    )
    new_text, n2 = bottom_re.subn(nav, text, count=1)
    if n2 > 0:
        text = new_text
    path.write_text(text, encoding='utf-8')
    print(fname, 'header_replaced', n1, 'bottom_replaced', n2)
