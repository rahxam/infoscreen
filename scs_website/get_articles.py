#!/usr/bin/env python3

import feedparser
import requests
import shutil
import hashlib
import json
import re
from pathlib import Path
import sqlite3 as s3
import os
from urlpath import URL

r = requests.get("https://sc-schwielochsee.de/index.php?option=com_ninjarsssyndicator&feed_id=1&format=raw")
feed = feedparser.parse( r.text )
del r

ozio_pattern = re.compile("{oziogallery __231__(\d+)}")
widgetkit_pattern = re.compile("\[widgetkit id=(\d+)\]")

con = s3.connect(str(((Path(__file__).resolve() / '..' / '..').resolve() / 'news.sqlite').resolve()))
with con:
	cur = con.cursor()
	for item in feed.entries:
		cur.execute("SELECT date FROM news WHERE foreign_id = ?",  (item['id'], ))
		result = cur.fetchone() 

		print(item['updated'])

		if result and result[0] != item['updated']:
			cur.execute("DELETE FROM news WHERE foreign_id = ?",  (item['id'], ))
			result = None

		if not result:
			r = requests.get(item['id'], params={'format' : 'json'})
			article = r.json()
			article['images'] = json.loads(article['images'])
			if len(article['images']['image_fulltext']) > 0 :
				path = Path("scs_website/files/articles/intro/" + hashlib.md5(str(article['images']['image_fulltext']).encode('utf-8')).hexdigest() + Path(article['images']['image_fulltext']).suffix).as_posix()
			
				cur.execute("INSERT INTO downloads (priority, url, path) VALUES (1, ?, ?)", ("https://sc-schwielochsee.de/" + article['images']['image_fulltext'], path))
			else:
				path = ""

			match = ozio_pattern.search(article['fulltext'])
			article['fulltext'] = re.sub(ozio_pattern, '', article['fulltext'])
			article['fulltext'] = re.sub(widgetkit_pattern, '', article['fulltext'])
			
			if match:
				google_album = match.group(1)

				os.makedirs("files/articles/" + google_album, exist_ok=True)

				picasa_r = requests.get("http://picasaweb.google.com/data/feed/api/user/SCSchwielochsee/albumid/" + google_album, params={'alt' : 'json'})
				picasa = picasa_r.json()

				counter = len(picasa['feed']['entry']) - 1
				if(counter > 5):
					counter = 5
				
				for i in range(0, counter):
					img_url = URL(picasa['feed']['entry'][i]['media$group']['media$content'][0]['url'])
					img_url = img_url.parents[0] / 's900' / img_url.name
					img_path = Path("scs_website/files/articles/" + google_album + "/" + hashlib.md5(str(img_url).encode('utf-8')).hexdigest() + img_url.suffix)

					cur.execute("INSERT INTO downloads (priority, url, path) VALUES (5, ?, ?)", (str(img_url), img_path.as_posix()))
			else:
				google_album = ""

			cur.execute("INSERT INTO news (foreign_id, date, published, text, title, image, author, google_album, type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'article')", (item['id'], item['updated'], article['publish_up'], article['fulltext'], article['title'], path, article['created_by_alias'], google_album))
			con.commit()
