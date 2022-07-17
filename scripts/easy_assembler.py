#!/usr/bin/env python3
import os
import sys

def main():
	if len(sys.argv) < 2:
		print(f"Usage: {sys.argv[0]} <file>")
		exit(0)
	filename = sys.argv[1][:len(sys.argv[1])-4]

	print(f"Assembling {filename}")

	os.system(f"nasm -f elf64 {sys.argv[1]} -o {filename}.o")
	os.system(f"ld {filename}.o -o {filename}")

if __name__ == "__main__":
	main()
