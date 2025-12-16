import sys
import time

lyrics = [
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum",
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum"
]

start_delay = 3.17   # şarkıyla senkron başlangıç

# %18 YAVAŞLATILMIŞ yeni süreler
pauses = [
    1.56,
    1.63,
    1.55,
    1.65
]

char_delay = 0.145  # harf harf hız

def type_line(text):
    for ch in text:
        sys.stdout.write(ch)
        sys.stdout.flush()
        time.sleep(char_delay)
    print()

print(f"{start_delay} saniye bekleniyor...\n")
time.sleep(start_delay)

for line, wait in zip(lyrics, pauses):
    type_line(line)
    time.sleep(wait)
