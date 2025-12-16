import sys
import time

# --- NAKARAT SÖZLERİ ---
lyrics = [
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum",
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum"
]

# --- AYARLAR ---
char_delay = 0.05   # harf harf yazma süresi (0.03–0.07 arası ideal)
pauses = [0.9, 1.1, 0.9, 1.1]  # şarkı temposuna uygun satır arası süreler

def type_line(text, delay):
    for ch in text:
        sys.stdout.write(ch)
        sys.stdout.flush()
        time.sleep(delay)
    print()

for line, wait in zip(lyrics, pauses):
    type_line(line, char_delay)
    time.sleep(wait)
