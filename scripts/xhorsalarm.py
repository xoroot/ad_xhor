#!/usr/bin/env python3
# Made for MacOS
# To keep curious eyes away from my laptop
# Instructions:
#   - Have a Homeparty with your Laptop as Spotify-Player
#   - Run the Script and background it quickly
#   - You have 10 Seconds until the alarm acivates
#   - So click on the Spotify Window to focus it (I would also make it fullscreen)
#   - As soon as the cursor clicks somewhere outside Spotify, a picture will be taken
#     and the MacBook goes to sleep/locks the screen
#   - You come back and unlock the Laptop with your fingerprint/password
#   - The Alarm is disabled, so you have to re-enable it (forward it again and press enter)
#   - Optional: put your custom sound-file into the commented playsound() function
#               (be aware: it's really loud! Don't be like me and record you screaming at them!)
#
# Have fun and be careful!
# ~XHOR


from AppKit import NSWorkspace
from time import sleep
from playsound import playsound
import os
import cv2
import random

cam = cv2.VideoCapture(0)
sleep(10)
safe = True
i = 1

def get_intruder():
	retval, img = cam.read()
	return img

while True:
	while safe:
		active_app = NSWorkspace.sharedWorkspace().activeApplication()
		if "Spotify" not in active_app['NSApplicationName']:
			

			# I figured out the shutter needed some preperation-time,
			# otherwise the captured image gets too dark
			for x in range(30):
				temp = cam.read()

			img = get_intruder()
			cv2.imwrite(f"intruders/intruder{i}.jpg", img)
			

			#playsound("alarm.m4a")
			os.system("pmset displaysleepnow")
			safe = False
		sleep(0.05)

	
	reset = input("Reset?")
	safe = True
	i = i + 1
	sleep(10)
