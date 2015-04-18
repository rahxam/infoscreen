#!/usr/bin/env python3

import tweepy
import sqlite3 as s3
from pathlib import Path

auth = tweepy.OAuthHandler("CEB1PBsHqUqF3LQ6fAHQGinvj", "fKdCHcIJZKLvt34cpFCIPyhd89BYfuPLJjVoenq4SSwHVkARRT")
auth.set_access_token("2474663810-ROMX1gs3hi7YYjkinCA87ZmoLQeaQoxpmN7J7Yg", "uzWj0Ctgf9atOLjdncwmxw4xcX80srioEkvKDEGL3sSWK")

api = tweepy.API(auth)

public_tweets = api.home_timeline(count=5)

con = s3.connect(str(((Path(__file__).resolve() / '..' / '..').resolve() / 'news.sqlite').resolve()))
with con:
    cur = con.cursor() 
    for tweet in public_tweets:

        cur.execute("SELECT foreign_id FROM news WHERE foreign_id = %s" % tweet.id_str) 
        result = cur.fetchone()
        print(tweet.text)

        if not result:
            print(tweet.id_str)
            cur.execute("INSERT INTO news (foreign_id, date, published, text, author, type) VALUES (?, ?, ?, ?, ?, 'twitter')", (tweet.id_str, tweet.created_at, tweet.created_at, tweet.text, tweet.author.screen_name))
            
