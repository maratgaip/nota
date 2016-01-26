DELIMITER //

CREATE PROCEDURE `import_song` (
	in i_id varchar(1000),
	in i_title varchar(1000),
    in i_artist varchar(1000)
)
BEGIN
	declare l_artist_id int default 0;
    declare l_song_id int default 0;
    declare l_url varchar(1000);
    
    set l_url = concat('http://media.super.kg/media/audio/a_', i_id, '.mp3');
    
    start transaction;
		set l_artist_id = (select distinct id from vass_artists where name = i_artist);
        if l_artist_id is null then
        begin
			insert into vass_artists (name, tag, bio, follow, link) values(i_artist, '', i_artist, 0, '');
            set l_artist_id = last_insert_id();
		end;
        end if;
        
        if (select count(1) from vass_songs where url = l_url and artist_id = l_artist_id) = 0 then
		begin
			delete from vass_songs where url = l_url;

			insert into vass_songs(song_country, user_id, created_on, url, artist_id, title, album_id, tags, lyrics, artists_id, last_loved, fav, played, purchase_url, permalink_url)
			values(116, 1, sysdate(), l_url, l_artist_id, i_title, 0, i_title, '', '', sysdate(), 0, 0, '', '');
		end;
        end if;
        
    commit;
    
END
