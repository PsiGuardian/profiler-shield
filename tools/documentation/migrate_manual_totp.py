#!/usr/bin/env python3
"""One-time, reproducible migration of existing translated TOTP enrolment text.

Do not invoke network services. This edits the published UI catalogues and the
documentation source before the existing 28-region generator is executed.
It intentionally does NOT change TOTP cryptography, MFA sessions or timeouts.
"""
from pathlib import Path
import re
ROOT = Path(__file__).resolve().parents[2]

# Locale-specific messages, reviewed for the manual-key setup flow.
# Columns: start, step2, step2_help, manual_secret.
MESSAGES = {
"fr": ("Configurer ma clé TOTP","Saisissez la clé dans votre authentificateur","Choisissez l’ajout manuel et saisissez la clé secrète affichée. Configurez SHA-1, six chiffres et une période de 30 secondes.","Clé secrète TOTP — saisie manuelle"),
"en": ("Set up my TOTP key","Enter the key in your authenticator","Choose manual account setup and enter the displayed secret key. Use SHA-1, six digits and a 30-second period.","TOTP secret key — manual entry"),
"de": ("TOTP-Schlüssel einrichten","Schlüssel in der Authenticator-App eingeben","Wählen Sie die manuelle Einrichtung und geben Sie den angezeigten Schlüssel ein. Verwenden Sie SHA-1, sechs Ziffern und 30 Sekunden.","TOTP-Geheimschlüssel — manuell eingeben"),
"es": ("Configurar mi clave TOTP","Introduzca la clave en su autenticador","Elija la configuración manual e introduzca la clave mostrada. Use SHA-1, seis dígitos y un periodo de 30 segundos.","Clave secreta TOTP — introducción manual"),
"it": ("Configura la chiave TOTP","Inserisci la chiave nell’autenticatore","Seleziona la configurazione manuale e inserisci la chiave mostrata. Usa SHA-1, sei cifre e 30 secondi.","Chiave segreta TOTP — inserimento manuale"),
"pt": ("Configurar a chave TOTP","Introduza a chave na aplicação","Escolha a configuração manual e introduza a chave apresentada. Utilize SHA-1, seis dígitos e um período de 30 segundos.","Chave secreta TOTP — introdução manual"),
"nl": ("TOTP-sleutel instellen","Voer de sleutel in de authenticator in","Kies handmatige configuratie en voer de getoonde sleutel in. Gebruik SHA-1, zes cijfers en een periode van 30 seconden.","Geheime TOTP-sleutel — handmatige invoer"),
"pl": ("Skonfiguruj klucz TOTP","Wpisz klucz w aplikacji uwierzytelniającej","Wybierz ręczną konfigurację i wpisz wyświetlony klucz. Ustaw SHA-1, sześć cyfr i okres 30 sekund.","Tajny klucz TOTP — wpis ręczny"),
"ro": ("Configurați cheia TOTP","Introduceți cheia în aplicația de autentificare","Alegeți configurarea manuală și introduceți cheia afișată. Folosiți SHA-1, șase cifre și un interval de 30 de secunde.","Cheie secretă TOTP — introducere manuală"),
"cs": ("Nastavit klíč TOTP","Zadejte klíč do autentizační aplikace","Zvolte ruční nastavení a zadejte zobrazený klíč. Použijte SHA-1, šest číslic a interval 30 sekund.","Tajný klíč TOTP — ruční zadání"),
"sk": ("Nastaviť kľúč TOTP","Zadajte kľúč do autentifikačnej aplikácie","Zvoľte ručné nastavenie a zadajte zobrazený kľúč. Použite SHA-1, šesť číslic a interval 30 sekúnd.","Tajný kľúč TOTP — ručné zadanie"),
"hu": ("TOTP-kulcs beállítása","Írja be a kulcsot a hitelesítő alkalmazásba","Válassza a kézi beállítást és írja be a megjelenített kulcsot. Használjon SHA-1-et, hat számjegyet és 30 másodperces időszakot.","Titkos TOTP-kulcs — kézi bevitel"),
"el": ("Ρύθμιση κλειδιού TOTP","Εισαγάγετε το κλειδί στην εφαρμογή επαλήθευσης","Επιλέξτε χειροκίνητη ρύθμιση και εισαγάγετε το κλειδί. Χρησιμοποιήστε SHA-1, έξι ψηφία και περίοδο 30 δευτερολέπτων.","Μυστικό κλειδί TOTP — χειροκίνητη εισαγωγή"),
"hr": ("Postavi TOTP ključ","Unesite ključ u aplikaciju za autentifikaciju","Odaberite ručnu postavku i unesite prikazani ključ. Koristite SHA-1, šest znamenki i razdoblje od 30 sekundi.","Tajni TOTP ključ — ručni unos"),
"ru": ("Настроить ключ TOTP","Введите ключ в приложение-аутентификатор","Выберите ручную настройку и введите показанный ключ. Используйте SHA-1, шесть цифр и период 30 секунд.","Секретный ключ TOTP — ручной ввод"),
"tr": ("TOTP anahtarını kur","Anahtarı doğrulama uygulamasına girin","Manuel kurulumu seçip gösterilen anahtarı girin. SHA-1, altı rakam ve 30 saniyelik süre kullanın.","TOTP gizli anahtarı — elle giriş"),
"ar": ("إعداد مفتاح TOTP","أدخل المفتاح في تطبيق المصادقة","اختر الإعداد اليدوي وأدخل المفتاح المعروض. استخدم SHA-1 وستة أرقام وفترة 30 ثانية.","مفتاح TOTP السري — إدخال يدوي"),
"hi": ("TOTP कुंजी सेट करें","प्रमाणीकरण ऐप में कुंजी दर्ज करें","मैन्युअल सेटअप चुनकर दिखाई गई कुंजी दर्ज करें। SHA-1, छह अंक और 30 सेकंड की अवधि चुनें।","TOTP गुप्त कुंजी — मैन्युअल प्रविष्टि"),
"zh": ("设置 TOTP 密钥","在身份验证应用中输入密钥","选择手动设置并输入显示的密钥。使用 SHA-1、六位数字和 30 秒周期。","TOTP 密钥 — 手动输入"),
"ja": ("TOTPキーを設定","認証アプリにキーを入力","手動設定を選び、表示されたキーを入力してください。SHA-1、6桁、30秒間隔を指定します。","TOTP秘密鍵 — 手動入力"),
"id": ("Atur kunci TOTP","Masukkan kunci ke aplikasi autentikator","Pilih penyiapan manual dan masukkan kunci yang ditampilkan. Gunakan SHA-1, enam digit, periode 30 detik.","Kunci rahasia TOTP — entri manual"),
"vi": ("Thiết lập khóa TOTP","Nhập khóa vào ứng dụng xác thực","Chọn thiết lập thủ công và nhập khóa đang hiển thị. Dùng SHA-1, sáu chữ số và chu kỳ 30 giây.","Khóa bí mật TOTP — nhập thủ công"),
"ka": ("TOTP გასაღების დაყენება","შეიყვანეთ გასაღები ავთენტიფიკატორში","აირჩიეთ ხელით დაყენება და შეიყვანეთ ნაჩვენები გასაღები. გამოიყენეთ SHA-1, ექვსი ციფრი და 30-წამიანი პერიოდი.","TOTP საიდუმლო გასაღები — ხელით შეყვანა"),
"mn": ("TOTP түлхүүр тохируулах","Түлхүүрийг баталгаажуулах аппд оруулна уу","Гараар тохируулахыг сонгож түлхүүр оруулна уу. SHA-1, зургаан орон, 30 секундын мөчлөг ашиглана.","TOTP нууц түлхүүр — гараар оруулах"),
"ms": ("Sediakan kunci TOTP","Masukkan kunci dalam aplikasi pengesah","Pilih persediaan manual dan masukkan kunci yang dipaparkan. Gunakan SHA-1, enam digit dan tempoh 30 saat.","Kunci rahsia TOTP — input manual"),
}

def read_catalogue(source: str, key: str) -> str:
    pattern = r"^\s*'" + re.escape(key) + r"'\s*=>\s*'(?P<text>(?:\\.|[^'\\])*)',\s*$"
    match = re.search(pattern, source, re.M)
    if not match:
        raise ValueError(f"Missing catalogue key {key}")
    return match["text"].replace("\\'", "'")

def write_catalogue(source: str, key: str, text: str) -> str:
    pattern = r"^(\s*'" + re.escape(key) + r"'\s*=>\s*)'(?:\\.|[^'\\])*'(\s*,\s*)$"
    value = text.replace("\\", "\\\\").replace("'", "\\'")
    changed, count = re.subn(pattern, lambda m: m[1] + "'" + value + "'" + m[2], source, count=1, flags=re.M)
    if count != 1:
        raise ValueError(f"Could not update key {key}")
    return changed

def sentences(value: str) -> list[str]:
    return re.findall(r"[^.!?。।؟]+[.!?。।؟]?", value) or [value]

def remove_qr_sentence(value: str) -> str:
    return "".join(sentence for sentence in sentences(value) if not re.search(r"QR|二维码", sentence, re.I)).strip()

def migrate():
    if len(MESSAGES) != 25:
        raise ValueError("All 25 base-language catalogues are required.")
    for lang, (start, heading, instruction, label) in MESSAGES.items():
        path = ROOT / "translations" / ("guardian." + lang + ".php")
        original = path.read_text(encoding="utf-8")
        guide = read_catalogue(original, "guide.mfa")
        lead = next((v.strip() for v in sentences(guide) if not re.search("QR|二维码", v, re.I)), "")
        guide = remove_qr_sentence(guide)
        guide = (guide + " " + instruction).strip()
        privacy = remove_qr_sentence(read_catalogue(original, "mfa.privacy_help"))
        if not lead or not guide or not privacy:
            raise ValueError(f"Failed to build localized text for {lang}")
        updates = {
            "mfa.lead": lead,
            "mfa.start": start,
            "mfa.step2": heading,
            "mfa.step2_help": instruction,
            "mfa.manual_secret": label,
            "mfa.privacy_help": privacy,
            "guide.mfa": guide,
        }
        for key, value in updates.items():
            if re.search(r"QR|二维码", value, re.I):
                raise ValueError(f"QR sentence still present in {lang}:{key}")
            original = write_catalogue(original, key, value)
        for key in ("mfa.local_only", "mfa.scan_help", "mfa.qr_alt", "mfa.qr_unavailable"):
            pattern = r"^\s*'" + re.escape(key) + r"'\s*=>.*\n"
            original, count = re.subn(pattern, "", original, count=1, flags=re.M)
            if count != 1:
                raise ValueError(f"Missing obsolete translation key {lang}:{key}")
        path.write_text(original, encoding="utf-8")
    path = ROOT / "tools/documentation/details.tsv"
    rows = []
    for line in path.read_text(encoding="utf-8").splitlines():
        if not line:
            continue
        lang, body = line.split("\t", 1)
        fields = body.split("|")
        if len(fields) != 10:
            raise ValueError(f"Bad TSV detail columns: {lang}")
        last = fields[9]
        index = re.search(r"QR|二维码|SVG|bacon", last, re.I)
        if index:
            prefix = last[:index.start()]
            markers = [prefix.rfind(x) for x in (".", "。", "।", "؟")]
            stop = max(markers)
            if stop < 0:
                raise ValueError(f"No sentence separator before QR reference: {lang}")
            fields[9] = prefix[:stop + 1].strip()
        if not fields[9] or re.search(r"QR|二维码|bacon|otpauth", fields[9], re.I):
            raise ValueError(f"Obsolete documentation source in {lang}")
        rows.append(lang + "\t" + "|".join(fields))
    path.write_text("\n".join(rows) + "\n", encoding="utf-8")
    print("25 localized TOTP catalogues and documentation source migrated.")

if __name__ == "__main__":
    migrate()
