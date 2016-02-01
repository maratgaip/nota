#!/bin/env python3
import sys
import mysql.connector as mysql
import requests

apiInit = requests.get('http://www.nota.kg/api/superkg/index.php', auth=('marat', 'marat'))
if apiInit.status_code != 200:
    print('Cannot initialize superkg API.')
    sys.exit(1)

jsonReq = requests.get('http://www.nota.kg/api/superkg/result.json', auth=('marat', 'marat'))
if jsonReq.status_code !=200:
    print('Cannot get results.json file.')
    sys.exit(1)

jsonData = jsonReq.json()
con = mysql.connect(host='127.0.0.1', user='root', password='Not@db', database='nota')

cur = con.cursor()

con.set_charset_collation('utf8')
cur.execute('SET NAMES utf8;')
cur.execute('SET CHARACTER SET utf8;')
cur.execute('SET character_set_connection=utf8;')

for song in jsonData['list']:
    songUrl = 'http://media.super.kg/media/audio/a_' + song['id'] + '.mp3'
    for artist in song['artist']:
        print(artist)
        title = song['song']
        print ('\t' + title)
        cur.execute("select id from vass_artists where name = %s;", (artist,))
        artistId = cur.fetchone()
        if artistId is None:
            cur.execute("insert into vass_artists (name, tag, bio, follow, link) values(%s, '', %s, 0, '');", (artist,artist))
            artistId = int(cur.lastrowid)
        else:
            artistId = artistId[0]

        cur.execute("select count(1) from vass_songs where url = %s and artist_id = %s;", (songUrl, artistId))
        if cur.fetchone()[0] == 0:
            cur.execute("""\
                insert into vass_songs(recent, song_country, user_id, created_on, url, artist_id, title, album_id, tags, lyrics, artists_id, last_loved, fav, played, purchase_url, permalink_url)
                values(1, 116, 1, sysdate(), %s, %s, %s, 0, '', '', '', sysdate(), 0, 0, '', '');
                            """,(songUrl, artistId, title)
                )

cur.close()
con.close()
