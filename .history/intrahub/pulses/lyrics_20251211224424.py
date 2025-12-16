import sys
import time

# Şarkı nakaratı
lyrics = [
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum",
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum"
]

# ŞARKIYLA TAM SENKRON AYARLAR
start_delay = 3.17   # Şarkıda nakaratın başladığı saniye

# Şarkı içindeki gerçek tempoya göre satır arası süreler
# (Şarkının ritmi: ~1.30 – 1.40 saniye arası)
pauses = [
    1.32,
    1.38,
    1.31,
    1.40
]

# Harf harf yazma hızı (şarkı ritmine göre çok hızlı değil)
char_delay = 0.045


def type_line(text):
    for ch in text:
        sys.stdout.write(ch)
        sys.stdout.flush()
        time.sleep(char_delay)
    print()


print(f"Şarkı ile senkron başlatmak için {start_delay} saniye bekleniyor...\n")
time.sleep(start_delay)

for line, wait in zip(lyrics, pauses):
    type_line(line)
    time.sleep(wait)
