import sys
import time

lyrics = [
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum",
    "Ölüyorum, ölüyorum",
    "Seni rüyalarda arıyorum"
]

def type_line(text, delay=0.05):
    for ch in text:
        sys.stdout.write(ch)
        sys.stdout.flush()
        time.sleep(delay)
    print()

for line in lyrics:
    type_line(line)
    time.sleep(1)
