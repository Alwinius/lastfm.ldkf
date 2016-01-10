#!/usr/bin/python
# -*- coding: utf-8 -*-
import requests, sys, unicodedata, re, json
method=sys.argv[1]
answer=requests.post("https://ws.audioscrobbler.com/2.0/?format=json&api_key=830d6e2d4d737d56aa1f94f717a477df&"+method)
content=answer.text
print content.encode('utf-8')