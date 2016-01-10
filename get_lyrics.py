#!/usr/bin/python
import requests, sys, unicodedata, re
url=sys.argv[1]
answer=requests.post(url)
content=answer.text
so=content[content.find('<div class="lyricsh">')+0:content.find('<form id="addsong"',content.find('<div class="lyricsh">'))]
print so.encode('utf-8')