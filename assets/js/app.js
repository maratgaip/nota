var Song = Backbone.Model.extend({
    defaults: {
        position: 0,
        title: "Unknown TitleMarat",
        artist: "Unknown Artist",
        listened: null
    },
    default_images: {
        small: null,
        medium: null,
        large: null
    },
    initialize: function() {
        if (!this.has("image")) {
            this.set({
                image: this.default_images
            })
        } else {
            var b = Math.ceil(Math.random() * DEFAULT_COVERART_NUM);
            var a = {
                small: player_root + "assets/images/album-bg-" + b + ".png",
                medium: player_root + "assets/images/album-bg-" + b + ".png",
                large: player_root + "assets/images/album-bg-" + b + ".png"
            };
            if (this.get("image").small == null) {
                this.set({
                    image: a
                })
            }
        }
        if (this.collection) {
            this.set({
                position: this.collection.length
            })
        }
        if (this.has("sources")) {
            this.set({
                source: this.get("sources")
            })
        }
        if (this.has("artists")) {
            this.set({
                source: this.get("artists")
            })
        }
        if (this.has("user_love")) {
            if (this.get("user_love").source != null) {
                this.set({
                    source: this.get("user_love").source
                })
            }
        } else {
        }
    },
    urlRoot: player_root + "more.php?t=song&songid=",
    url: function() {
        return this.urlRoot + this.get("id")
    },
    parse: function(a) {
        return a.song
    },
    toJSON: function() {
        var e = _.clone(this.attributes);
        if (!this.has("title")) {
            e.title = this.defaults.title
        }
        if (!this.has("artist")) {
            e.artist = this.defaults.artist
        }
        e.ordered_loves = [];
        if (this.has("user_love")) {
            e.ordered_loves.push(this.get("user_love"))
        }
        if (this.has("viewer_love")) {
            if (this.has("user_love")) {
                if (this.get("user_love").username != this.get("viewer_love").username) {
                    e.ordered_loves.push(this.get("viewer_love"))
                }
            } else {
                e.ordered_loves.push(this.get("viewer_love"))
            }
        }
        if (this.has("recent_loves")) {
            var d = this.get("recent_loves");
            for (var c = 0; c < d.length; c++) {
                e.ordered_loves.push(d[c])
            }
        }
        e.ordered_loves.sort(function(g, f) {
            return new Date(g.created_on).getTime() - new Date(f.created_on).getTime()
        });
        var b = this.get("artist");
        if (!this.has("artist")) {
            b = this.get("title")
        }
        e.buy_link = AFF_BUY_LINK + b;
        if (this.has("buy_link")) {
            var a = decodeURI(this.get("buy_link"));
            e.buy_link = a.replace("%26tag%3Dws", "%26tag%3Dext0a-20")
        }
        return e
    }
});
var SongCollection = Backbone.Collection.extend({
    start: 0,
    results: 20,
    model: Song,
    initialize: function(a) {
        _.extend(this, a)
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var TrendingSongCollection = SongCollection.extend({
    url: function() {
        return player_root + "more.php?t=" + this.path + "&genre=" + this.genre + "&start=" + this.start + "&results=" + this.results
    }
});
var SongLovedFeedCollection = SongCollection.extend({
    hasMore: false,
    url: function() {
        return player_root + "more.php?t=member&username=" + this.username + "&action=feedlove&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return _.map(a.activities, function(b) {
            return b.object
        })
    }
});
var TimelineLovedCollection = SongCollection.extend({
    hasMore: false,
    url: function() {
        return player_root + "more.php?t=last_loved&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return _.map(a.activities, function(b) {
            return b.object
        })
    }
});
var LatestPlaylistCollection = SongCollection.extend({
    hasMore: false,
    url: function() {
        return player_root + "more.php?t=last_playlists&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return _.map(a.activities, function(b) {
            return b.object
        })
    }
});
var SongGenreCollection = SongCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=genre&name=" + this.genre + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var TOPSongsCollection = SongCollection.extend({
    url: function() {
        return player_root + "more.php?t=top&date=" + this.date + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var UserLovedCollection = SongCollection.extend({
    username: null,
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=member&username=" + this.username + "&action=loved&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var SongSearchCollection = SongCollection.extend({
    hasMore: false,
    totalResults: 0,
    url: function() {
        return player_root + "more.php?t=search&q=" + this.q + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        this.totalResults = a.total;
        return a.songs
    }
});
var User = Backbone.Model.extend({
    initialize: function() {
        if (this.get("name") != null && this.get("name") != "") {
            this.set({
                display_name: this.get("name")
            })
        } else {
            this.set({
                display_name: this.get("username")
            })
        }
        this.set({
            id: this.get("username")
        })
    },
    urlRoot: player_root + "more.php?t=member&username=",
    url: function() {
        return this.urlRoot + this.get("username")
    },
    parse: function(a) {
        return a.user
    }
});
var UserCollection = Backbone.Collection.extend({
    start: 0,
    results: 20,
    model: User
});
var UserFollowingCollection = UserCollection.extend({
    username: null,
    hasMore: true,
    sortBy: "username",
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "comparator")
    },
    url: function() {
        return player_root + "more.php?t=member&username=" + this.username + "&action=following&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.following
    },
    comparator: function(a) {
        return a.get(this.sortBy)
    }
});

var UserPlaylistCollection = UserCollection.extend({
    username: null,
    hasMore: true,
    sortBy: "username",
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "comparator")
    },
    url: function() {
        return player_root + "more.php?t=member&username=" + this.username + "&action=playlist&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.playlists
    },
    comparator: function(a) {
        return a.get(this.sortBy)
    }
});
var UserFollowersCollection = UserCollection.extend({
    username: null,
    hasMore: true,
    initialize: function(a) {
        _.extend(this, a)
    },
    url: function() {
        return player_root + "more.php?t=member&username=" + this.username + "&action=followers&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.followers
    }
});
var MeUser = User.extend({
    url: function() {
        return player_root + "more.php?t=me"
    },
    parse: function(a) {
        return a.user
    }
});
var TastemakersCollection = UserCollection.extend({
    hasMore: false,
    initialize: function(a) {
        _.extend(this, a)
    },
    url: function() {
        return player_root + "more.php?t=member&action=tastemakers&?start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.following
    }
});
var MaybeFriendsCollection = UserCollection.extend({
    username: null,
    hasMore: false,
    initialize: function(a) {
        _.extend(this, a)
    },
    url: function() {
        return player_root + "more.php?t=user&username=" + this.username + "&action=maybe-friends&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.users
    }
});
var UserNotification = Backbone.Model.extend({
    initialize: function() {
        if (this.collection) {
            this.set({
                position: this.collection.length
            })
        }
    }
});
var UserNotificationCollection = Backbone.Collection.extend({
    start: 0,
    results: 20,
    username: null,
    model: UserNotification,
    initialize: function(a) {
        _.extend(this, a)
    },
    url: function() {
        return player_root + "more.php?t=member&username=" + this.username + "&action=notifications&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.sites
    }
});
var AOTW = Backbone.Model.extend();
var AOTWCollection = Backbone.Collection.extend({
    start: 0,
    results: 1,
    model: AOTW,
    initialize: function(a) {
        _.extend(this, a)
    },
    url: function() {
        return player_root + "more.php?t=aotw&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        return a.albums
    }
});
var SongGraph = Backbone.Model.extend({
    url: function() {
        return player_root + "api/song/" + this.get("id") + "/graph"
    },
    parse: function(a) {
        return a.graph
    }
});
var SongView = Backbone.View.extend({
    showAvatar: true,
    user: null,
    template: Templates.common_songs,
    tagName: "div",
    className: "song_row a_song",
    events: {
        "click .song_view_love": "onLovedClicked",
        "click .song_view_share": "onShareClicked",
        "click .song_view_embed": "onEmbedClicked",
        "click .song_view_play_button": "onPlayButtonClicked",
        "click .song_view_queue": "onQueueClicked",
        "click .song_view_remove": "onRemoveClicked"
    },
    initialize: function(a) {
        _.extend(this, a);
        this.model.view = this;
        this.model.bind("change", this.render)
    },
    render: function() {
        $(this.el).html(this.template({
            song: this.model.toJSON(),
            user: this.user,
            showAvatar: this.showAvatar
        }));
        var c = this.model.get("id");
        $(this.el).attr("song_id", c);
        $(this.el).addClass("a_song_" + c);
        var b = parseInt(this.model.get("position")) % 2 == 0;
        $(this.el).addClass(b + "");
        try {
            if (AudioPlayer.List.current[AudioPlayer.QueueNumber].id == c) {
                $(this.el).addClass("playing")
            }
        } catch (a) {
        }
        return this
    },
    onPlayButtonClicked: function(b) {
        if ($(b.target).parents(".playing").length != 1) {
            var el = $(this.el).find('.song_view_queue').addClass("added");
			 if (loggedInUser == null) {

            alert("You must be logged in to listen songs.", true)
			}else{
            $(window).trigger({
                "type": "lalaQueueSong",
                "song": this.model.toJSON()
            });
			}
            var song_pos = AudioPlayer.List.current.length - 1;
            $(window).trigger({
                type: "lalaNewSongList",
                list: AudioPlayer.List.current,
                position: song_pos,
                section: this.section
            })
        }


        return false
    },
    onLovedClicked: function(b) {
        var a = $(this.el).find(".song_view_love").addClass("loading");
        $(window).trigger({
            type: (a.hasClass("on")) ? "lalaUnLoveSong" : "lalaLoveSong",
            song: this.model.toJSON()
        });
        return false
    },
    onShareClicked: function(a) {
        if (loggedInUser == null) {

            alert("You must be logged in to share the song.", true)
        } else {
            $(window).trigger({
                type: "lalaShareSong",
                song: this.model.toJSON()
            });
            return false
        }
    },
    onEmbedClicked: function(a) {
        if (loggedInUser == null) {

            alert("You must be logged in to get this embed code", true)
        } else {
            $(window).trigger({
                type: "lalaEmbedSong",
                song: this.model.toJSON()
            });
            return false
        }
    },
    onQueueClicked: function(b) {
        $('#dropdown-1').hide();
        var songinfo = this.model.toJSON();
        SONG_PRE_ADD = songinfo.id;
        var a = $(this.el).find(".song_view_queue");
        PlaylistMenu.Show(a);
        $("#add_to_queue_click").bind("click", function() {
            a.addClass("added");
            $('#dropdown-1').hide();
            $(window).trigger({
                type: "lalaQueueSong",
                song: songinfo
            });
            jQuery(window).trigger({
                type: "lalaNewPlaylist",
                list: AudioPlayer.List.current
            });
            $("#add_to_queue_click").unbind("click");
        });
        return false
    },
    onRemoveClicked: function(b) {
        var songinfo = this.model.toJSON();
        var a = $(this.el).fadeOut("slow");
        $.ajax({
            type: "POST",
            url: player_root + "more.php",
            data: {
                song_id: songinfo.id,
                playlist_id: songinfo.playlist_id,
                t: "playlist",
                action: "remove_song"
            },
            success: function(response) {
                return false;
            }
        });
        return false
    }
});
if (typeof (EmbedBox) == "undefined") {
    EmbedBox = {}
}
EmbedBox.Listen = function(a) {
    EmbedBox.Show(a.song)
};

EmbedBox.Song = null;
EmbedBox.IsShowing = false;
EmbedBox.Show = function(b) {
    if (EmbedBox.IsShowing == false) {
        EmbedBox.Song = b;
//        console.log(EmbedBox.Song.id);

        EmbedBox.Song.id = Base64.encode(EmbedBox.Song.id);
//      console.log(EmbedBox.Song.id);
        jQuery("#full_cover").removeClass("display_none");
        var a = Templates.embed_box({
            song: EmbedBox.Song
        });
        jQuery(document.body).append(a);
        EmbedBox.AddListeners();
        EmbedBox.IsShowing = true
    }
};
EmbedBox.Hide = function() {
    jQuery("#embed_box_close_button").unbind("click", EmbedBox.Hide);
    jQuery("#embed_box").remove();

    jQuery("#full_cover").addClass("display_none");

    EmbedBox.Song = null;

    EmbedBox.IsShowing = false
};
EmbedBox.AddListeners = function() {
    jQuery("#embed_box_close_button").bind("click", EmbedBox.Hide);

};
jQuery(window).bind("lalaEmbedSong", EmbedBox.Listen);


if (typeof (EmbedBoxAlbum) == "undefined") {
    EmbedBoxAlbum = {}
}
EmbedBoxAlbum.Listen = function(a) {
    //console.log(a.album);
    EmbedBoxAlbum.Show(a.album)

};

EmbedBoxAlbum.album = null;
EmbedBoxAlbum.IsShowing = false;
EmbedBoxAlbum.Show = function(b) {
    if (EmbedBoxAlbum.IsShowing == false) {
        EmbedBoxAlbum.album = b;
        EmbedBoxAlbum.album = Base64.encode(EmbedBoxAlbum.album);
        jQuery("#full_cover").removeClass("display_none");
        var a = Templates.embed_box_album({
            album: EmbedBoxAlbum.album
        });
        jQuery(document.body).append(a);
        EmbedBoxAlbum.AddListeners();
        EmbedBoxAlbum.IsShowing = true
    }
};
EmbedBoxAlbum.Hide = function() {
    jQuery("#embed_box_close_button").unbind("click", EmbedBoxAlbum.Hide);
    jQuery("#embed_box_album").remove();
    jQuery("#full_cover").addClass("display_none");

    EmbedBoxAlbum.album = null;

    EmbedBoxAlbum.IsShowing = false
};
EmbedBoxAlbum.AddListeners = function() {
    jQuery("#embed_box_close_button").bind("click", EmbedBoxAlbum.Hide);

};
jQuery(window).bind("lalaEmbedAlbum", EmbedBoxAlbum.Listen);

jQuery(".song_view_embed_album").live("click", function() {
    if (loggedInUser == null) {

        alert("You must be logged in to get this embed code", true)
    } else {
        var g = $(this).attr('data-info');

        $(window).trigger({
            type: "lalaEmbedAlbum",
            album: g

        });
    }
});


if (typeof (EmbedBoxPlaylist) == "undefined") {
    EmbedBoxPlaylist = {}
}
EmbedBoxPlaylist.Listen = function(a) {
    EmbedBoxPlaylist.Show(a.playlist)
};

EmbedBoxPlaylist.playlist = null;
EmbedBoxPlaylist.IsShowing = false;
EmbedBoxPlaylist.Show = function(b) {
    if (EmbedBoxPlaylist.IsShowing == false) {
        EmbedBoxPlaylist.playlist = b;
        EmbedBoxPlaylist.playlist = Base64.encode(EmbedBoxPlaylist.playlist);
        jQuery("#full_cover").removeClass("display_none");
        var a = Templates.embed_box_playlist({
            playlist: EmbedBoxPlaylist.playlist
        });
        jQuery(document.body).append(a);
        EmbedBoxPlaylist.AddListeners();
        EmbedBoxPlaylist.IsShowing = true
    }
};
EmbedBoxPlaylist.Hide = function() {
    jQuery("#embed_box_close_button").unbind("click", EmbedBoxPlaylist.Hide);
    jQuery("#embed_box_playlist").remove();
    jQuery("#full_cover").addClass("display_none");

    EmbedBoxPlaylist.playlist = null;

    EmbedBoxPlaylist.IsShowing = false
};
EmbedBoxPlaylist.AddListeners = function() {
    jQuery("#embed_box_close_button").bind("click", EmbedBoxPlaylist.Hide);

};
jQuery(window).bind("lalaEmbedPlaylist", EmbedBoxPlaylist.Listen);

jQuery(".song_view_embed_playlist").live("click", function() {
    if (loggedInUser == null) {

        alert("You must be logged in to get this embed code", true)
    } else {
        var f = $(this).attr('data-info');
        $(window).trigger({
            type: "lalaEmbedPlaylist",
            playlist: f
        });
    }
});

var SongListView = Backbone.View.extend({
    el: $("#song_list"),
    collectionClass: null,
    show_user: true,
    show_user_in_others: false,
    itemRows: null,
    scrollDiv: $("#right"),
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch", "onPlayAllClicked");
        $("#right").unbind();
        this.songs = new this.collectionClass(a);
        this.songs.bind("add", this.onAdd);
        this.songs.bind("reset", this.onInitialFetch);
        this.songs.bind("fetch", this.onFetch);
        this.render();
        this.itemRows = $("#item_rows");
        this.scrollDiv = $("#right");
        $(this.scrollDiv).unbind();
        this.songs.fetch();
        $(".song_top_play_all").bind("click", this.onPlayAllClicked);
        $(".song_top_play_all1").bind("click", this.onPlayAllClicked);
        //   $(".album_like").bind("click",this.onlikeClicked);
    },
    scrollFeed: function(c) {
        var a = $(".song_tabs");
        if (a.length > 0) {
            if (this.fromtop == undefined) {
                this.fromtop = a.offset().top
            }
            var b = $(c.target);
            var d = b.scrollTop();
            if (d + 44 >= this.fromtop) {
                a.addClass("fixed")
            } else {
                if (a.hasClass("fixed")) {
                    a.removeClass("fixed")
                }
            }
        }
    },
    onInitialFetch: function(a) {
        $(this.scrollDiv).scrollTop(0);
        $(this.itemRows).empty();
        this.songs.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        b.set({
            show_user: this.show_user,
            show_user_in_others: this.show_user_in_others
        });
        var a = new SongView({
            model: b,
            section: this.section
        });
        $(this.itemRows).append(a.render().el)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        $(this.scrollDiv).unbind();
        jQuery("#load_more").remove();
        if (this.songs.hasMore == true) {
            var a = Templates.list_load_more();
            $(this.itemRows).append(a);
            $(this.scrollDiv).bind("scroll", Utils.ScrollBottom);
            $(this.scrollDiv).bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $(this.scrollDiv).unbind("scrollBottom", this.onScrollBottom);
        if (this.songs.hasMore) {
            this.songs.start += this.songs.results;
            this.songs.fetch({
                add: true
            })
        }
    },
    onPlayAllClicked: function(a) {
        $(window).trigger({
            type: "lalaNewSongList",
            list: this.songs.toJSON(),
            position: 0,
            section: this.section
        });
        return false
    }

});
var LPlaylistView = Backbone.View.extend({
    showAvatar: true,
    user: null,
    template: Templates.common_latest_playlist,
    tagName: "div",
    className: "song_row a_song",
    events: {
        "click .song_view_love": "onLovedClicked",
        "click .song_view_share": "onShareClicked",
        "click .song_view_play_button": "onPlayButtonClicked",
        "click .song_view_queue": "onQueueClicked",
        "click .song_view_remove": "onRemoveClicked"
    },
    initialize: function(a) {
        _.extend(this, a);
        this.model.view = this;
        this.model.bind("change", this.render)
    },
    render: function() {
        $(this.el).html(this.template({
            playlist: this.model.toJSON(),
            user: this.user,
            showAvatar: this.showAvatar
        }));
        var c = this.model.get("id");
        $(this.el).attr("song_id", c);
        $(this.el).addClass("a_song_" + c);
        var b = parseInt(this.model.get("position")) % 2 == 0;
        $(this.el).addClass(b + "");
        try {
            if (AudioPlayer.List.current[AudioPlayer.QueueNumber].id == c) {
                $(this.el).addClass("playing")
            }
        } catch (a) {
        }
        return this
    },
    onPlayButtonClicked: function(b) {
        if ($(b.target).parents(".playing").length != 1) {
            var a = [this.model.toJSON()];
            if (this.model.collection) {
                a = this.model.collection.toJSON()
            }
            $(window).trigger({
                type: "lalaNewSongList",
                list: a,
                position: this.model.get("position"),
                section: this.section
            })
        }
        return false
    },
    onLovedClicked: function(b) {
        var a = $(this.el).find(".song_view_love").addClass("loading");
        $(window).trigger({
            type: (a.hasClass("on")) ? "lalaUnLoveSong" : "lalaLoveSong",
            song: this.model.toJSON()
        });
        return false
    },
    onShareClicked: function(a) {
        $(window).trigger({
            type: "lalaShareSong",
            song: this.model.toJSON()
        });
        return false
    },
    onQueueClicked: function(b) {
        $('#dropdown-1').hide();
        var songinfo = this.model.toJSON();
        SONG_PRE_ADD = songinfo.id;
        var a = $(this.el).find(".song_view_queue");
        PlaylistMenu.Show(a);
        $("#add_to_queue_click").bind("click", function() {
            a.addClass("added");
            $('#dropdown-1').hide();
            $(window).trigger({
                type: "lalaQueueSong",
                song: songinfo
            });
            jQuery(window).trigger({
                type: "lalaNewPlaylist",
                list: AudioPlayer.List.current
            });
            $("#add_to_queue_click").unbind("click");
        });
        return false
    },
    onRemoveClicked: function(b) {
        var songinfo = this.model.toJSON();
        var a = $(this.el).fadeOut("slow");
        $.ajax({
            type: "POST",
            url: player_root + "more.php",
            data: {
                song_id: songinfo.id,
                playlist_id: songinfo.playlist_id,
                t: "playlist",
                action: "remove_song"
            },
            success: function(response) {
                return false;
            }
        });
        return false
    }
});
var PlaylistListView = Backbone.View.extend({
    el: $("#song_list"),
    collectionClass: null,
    show_user: true,
    show_user_in_others: false,
    itemRows: null,
    scrollDiv: $("#right"),
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch");
        $("#right").unbind();
        this.playlists = new this.collectionClass(a);
        this.playlists.bind("add", this.onAdd);
        this.playlists.bind("reset", this.onInitialFetch);
        this.playlists.bind("fetch", this.onFetch);
        this.render();
        this.itemRows = $("#item_rows");
        this.scrollDiv = $("#right");
        $(this.scrollDiv).unbind();
        this.playlists.fetch();
        $(".song_top_play_all").bind("click", this.onPlayAllClicked);
        $(".song_top_play_all1").bind("click", this.onPlayAllClicked);
    },
    scrollFeed: function(c) {
        var a = $(".song_tabs");
        if (a.length > 0) {
            if (this.fromtop == undefined) {
                this.fromtop = a.offset().top
            }
            var b = $(c.target);
            var d = b.scrollTop();
            if (d + 44 >= this.fromtop) {
                a.addClass("fixed")
            } else {
                if (a.hasClass("fixed")) {
                    a.removeClass("fixed")
                }
            }
        }
    },
    onInitialFetch: function(a) {
        $(this.scrollDiv).scrollTop(0);
        $(this.itemRows).empty();
        this.playlists.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        b.set({
            show_user: this.show_user,
            show_user_in_others: this.show_user_in_others
        });
        var a = new LPlaylistView({
            model: b,
            section: this.section
        });
        $(this.itemRows).append(a.render().el)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        $(this.scrollDiv).unbind();
        jQuery("#load_more").remove();
        if (this.playlists.hasMore == true) {
            var a = Templates.list_load_more();
            $(this.itemRows).append(a);
            $(this.scrollDiv).bind("scroll", Utils.ScrollBottom);
            $(this.scrollDiv).bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $(this.scrollDiv).unbind("scrollBottom", this.onScrollBottom);
        if (this.playlists.hasMore) {
            this.playlists.start += this.playlists.results;
            this.playlists.fetch({
                add: true
            })
        }
    }
});
$(window).bind("lalaSongLoved", function(a) {
    $(".song_view_love_" + a.song.id).removeClass("loading");
    if (a.success == true) {
        $(".song_view_love_" + a.song.id).addClass("on").attr("tooltip", "Loved")
    }
});
$(window).bind("lalaSongUnLoved", function(a) {
    $(".song_view_love_" + a.song.id).removeClass("loading");
    if (a.success == true) {
        $(".song_view_love_" + a.song.id).removeClass("on").attr("tooltip", "Love this song")
    }
});
$(window).bind("lalaAudioNewSong", function(a) {
    jQuery(".a_song").removeClass("playing");
    jQuery(".a_song_" + a.song.id).addClass("playing")
});
var UserView = Backbone.View.extend({
    template: Templates.common_users,
    initialize: function(a) {
        _.extend(this, a);
        this.model.view = this;
        this.model.bind("change", this.render)
    },
    render: function() {
        $(this.el).html(this.template({
            user: this.model.toJSON(),
            position: this.model.get("position")
        }));
        return this
    }
});
var PListView = Backbone.View.extend({
    template: Templates.common_playlists,
    initialize: function(a) {
        _.extend(this, a);
        this.model.view = this;
        this.model.bind("change", this.render)
    },
    render: function() {
        $(this.el).html(this.template({
            playlist: this.model.toJSON(),
            position: this.model.get("position")
        }));
        return this
    }
});
var UserListView = Backbone.View.extend({
    className: "display_none",
    collectionClass: null,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch");
        $("#right").unbind();
        this.users = new this.collectionClass(a);
        this.users.bind("add", this.onAdd);
        this.users.bind("reset", this.onInitialFetch);
        this.users.bind("fetch", this.onFetch);
        this.users.fetch();
        this.render()
    },
    onInitialFetch: function(a) {
        $("#right").scrollTop(0);
        $("#item_rows").empty();
        this.users.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        var a = new UserView({
            model: b
        });
        $(this.el).append(a.render().el)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        jQuery("#load_more").remove();
        $("#right").unbind();
        if (this.users.hasMore == true) {
            var a = Templates.list_load_more();
            $("#item_rows").append(a);
            $("#right").bind("scroll", Utils.ScrollBottom);
            $("#right").bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $("#right").unbind("scrollBottom", this.onScrollBottom);
        if (this.users.hasMore) {
            this.users.start += this.users.results;
            this.users.fetch({
                add: true
            })
        }
    }
});
var PlaylistView = Backbone.View.extend({
    className: "display_none",
    collectionClass: null,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch");
        $("#right").unbind();
        this.users = new this.collectionClass(a);
        this.users.bind("add", this.onAdd);
        this.users.bind("reset", this.onInitialFetch);
        this.users.bind("fetch", this.onFetch);
        this.users.fetch();
        this.render()
    },
    onInitialFetch: function(a) {
        $("#right").scrollTop(0);
        $("#item_rows").empty();
        this.users.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        var a = new PListView({
            model: b
        });
        $(this.el).append(a.render().el)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        jQuery("#load_more").remove();
        $("#right").unbind();
        if (this.users.hasMore == true) {
            var a = Templates.list_load_more();
            $("#item_rows").append(a);
            $("#right").bind("scroll", Utils.ScrollBottom);
            $("#right").bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $("#right").unbind("scrollBottom", this.onScrollBottom);
        if (this.users.hasMore) {
            this.users.start += this.users.results;
            this.users.fetch({
                add: true
            })
        }
    }
});

var LeftUserView = Backbone.View.extend({
    template: Templates.left_following,
    tagName: "li",
    initialize: function() {
        if (this.model.collection) {
            this.model.set({
                position: this.model.collection.indexOf(this.model)
            })
        }
        this.model.view = this;
        this.model.bind("change", this.render)
    },
    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        $(this.el).attr("position", this.model.get("position"));
        $(this.el).attr("username", this.model.get("username"));
        return this
    }
});
var LeftUserPlaylist = Backbone.View.extend({
    template: Templates.left_playlist,
    tagName: "li",
    initialize: function() {
        if (this.model.collection) {
            this.model.set({
                position: this.model.collection.indexOf(this.model)
            })
        }
        this.model.view = this;
        this.model.bind("change", this.render)
    },
    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        $(this.el).attr("position", this.model.get("position"));
        $(this.el).attr("id", this.model.get("playlist_id"));
        return this
    }
});
if (typeof (Settings) == "undefined") {
    Settings = {}
}
Settings.Change = function(c) {
    if (c.href.indexOf("settings") != -1) {
        if (loggedInUser != null) {
            jQuery("#right").attr("class", "settings");
            var a = Templates.settings()
        } else {
            alert('Please <a href="/sign-in">login</a> to use this feature!');
            return false
        }
        $("#right").unbind("scrollBottom", this.onScrollBottom);
        jQuery("#settings").html(a);
        Utils.HideSections("#settings");
        Utils.SetUserBackground("#right", userBackground);
        var d = c.href.substring(c.href.lastIndexOf("/") + 1);
        $("#settings").attr("class", d);
        switch (c.href) {
            case "settings/profile":
                jQuery(window).trigger({
                    type: "lalaSettingsProfile"
                });
                break;

            case "settings/social":
                jQuery(window).trigger({
                    type: "lalaSettingsSocial"
                });
                break;
            case "settings/connections":
                jQuery(window).trigger({
                    type: "lalaSettingsConnections"
                });
                break;
            case "settings/design":
                jQuery(window).trigger({
                    type: "lalaSettingsDesign"
                });
                break;
            case "settings/find-friends":
                jQuery(window).trigger({
                    type: "lalaSettingsFriends"
                });
                break;
            case "settings/account":
                jQuery(window).trigger({
                    type: "lalaSettingsAccount"
                });
                break;
            case "settings/notifications":
                var b = new SettingsNotificationsView({
                    el: "#settings_middle"
                });
                break;
            default:
                jQuery(window).trigger({
                    type: "lalaSettingsProfile"
                });
                break
        }
    }
};
Settings.LoggedIn = function(a) {
    jQuery("#settings_top").text(a.user.username + "'s settings")
};
jQuery(window).bind("lalaHistoryChange", Settings.Change);
jQuery(window).bind("lalaUserLoggedIn", Settings.LoggedIn);
if (typeof (Storage) == "undefined") {
    Storage = {}
}
Storage.Set = function(a, c) {
    var b = JSON.stringify(c);
    localStorage.setItem(a, b)
};
Storage.Get = function(a) {
    var c = null;
    try {
        var d = localStorage.getItem(a);
        if (d != null) {
            c = JSON.parse(d)
        }
    } catch (b) {
    }
    return c
};
Storage.Remove = function(a) {
    try {
        localStorage.removeItem(a)
    } catch (b) {
    }
};
jQuery(document).ready(function() {
    Storage.Remove("browse");
    Storage.Remove("browseObj")
});
(function(b) {
    b.fn.dragsort = function(j) {
        var i = b.extend({}, b.fn.dragsort.defaults, j);
        var a = [];
        var e = null,
                h = null;
        if (this.selector) {
            this.each(function(d, f) {
                if (b(f).is("table") && b(f).children().size() == 1 && b(f).children().is("tbody")) {
                    f = b(f).children().get(0)
                }
                var c = {
                    draggedItem: null,
                    placeHolderItem: null,
                    pos: null,
                    offset: null,
                    offsetLimit: null,
                    scroll: null,
                    container: f,
                    init: function() {
                        b(this.container).unbind();
                        b(this.container).attr("data-listIdx", d).mousedown(this.grabItem).find(i.dragSelector).css("cursor", "pointer");
                        b(this.container).children(i.itemSelector).each(function(g) {
                            b(this).attr("data-itemIdx", g)
                        })
                    },
                    grabItem: function(w) {
                        if (w.which != 1 || b(w.target).is(i.dragSelectorExclude)) {
                            return
                        }
                        var t = w.target;
                        while (!b(t).is("[data-listIdx='" + b(this).attr("data-listIdx") + "'] " + i.dragSelector)) {
                            if (t == this) {
                                return
                            }
                            t = t.parentNode
                        }
                        if (e != null && e.draggedItem != null) {
                            e.dropItem()
                        }
                        e = a[b(this).attr("data-listIdx")];
                        e.draggedItem = b(t).closest(i.itemSelector);
                        var g = parseInt(e.draggedItem.css("marginTop"));
                        var u = parseInt(e.draggedItem.css("marginLeft"));
                        e.offset = e.draggedItem.offset();
                        e.offset.top = w.pageY - e.offset.top + (isNaN(g) ? 0 : g) - 1;
                        e.offset.left = w.pageX - e.offset.left + (isNaN(u) ? 0 : u) - 1;
                        if (!i.dragBetween) {
                            var x = b(e.container).outerHeight() == 0 ? Math.max(1, Math.round(0.5 + b(e.container).children(i.itemSelector).size() * e.draggedItem.outerWidth() / b(e.container).outerWidth())) * e.draggedItem.outerHeight() : b(e.container).outerHeight();
                            e.offsetLimit = b(e.container).offset();
                            e.offsetLimit.right = e.offsetLimit.left + b(e.container).outerWidth() - e.draggedItem.outerWidth();
                            e.offsetLimit.bottom = e.offsetLimit.top + x - e.draggedItem.outerHeight()
                        }
                        var y = e.draggedItem.height();
                        var k = e.draggedItem.width();
                        var v = e.draggedItem.attr("style");
                        e.draggedItem.attr("data-origStyle", v ? v : "");
                        if (i.itemSelector == "tr") {
                            e.draggedItem.children().each(function() {
                                b(this).width(b(this).width())
                            });
                            e.placeHolderItem = e.draggedItem.clone().attr("data-placeHolder", true);
                            e.draggedItem.after(e.placeHolderItem);
                            e.placeHolderItem.children().each(function() {
                                b(this).css({
                                    borderWidth: 0,
                                    width: b(this).width() + 1,
                                    height: b(this).height() + 1
                                }).html("&nbsp;")
                            })
                        } else {
                            e.draggedItem.after(i.placeHolderTemplate);
                            e.placeHolderItem = e.draggedItem.next().css({
                                height: y,
                                width: k
                            }).attr("data-placeHolder", true)
                        }
                        e.draggedItem.css({
                            position: "absolute",
                            opacity: 0.8,
                            "z-index": 999,
                            height: y,
                            width: k
                        });
                        b(a).each(function(l, m) {
                            m.createDropTargets();
                            m.buildPositionTable()
                        });
                        e.scroll = {
                            moveX: 0,
                            moveY: 0,
                            maxX: b(document).width() - b(window).width(),
                            maxY: b(document).height() - b(window).height()
                        };
                        e.scroll.scrollY = window.setInterval(function() {
                            if (i.scrollContainer != window) {
                                b(i.scrollContainer).scrollTop(b(i.scrollContainer).scrollTop() + e.scroll.moveY);
                                return
                            }
                            var l = b(i.scrollContainer).scrollTop();
                            if (e.scroll.moveY > 0 && l < e.scroll.maxY || e.scroll.moveY < 0 && l > 0) {
                                b(i.scrollContainer).scrollTop(l + e.scroll.moveY);
                                e.draggedItem.css("top", e.draggedItem.offset().top + e.scroll.moveY + 1)
                            }
                        }, 10);
                        e.scroll.scrollX = window.setInterval(function() {
                            if (i.scrollContainer != window) {
                                b(i.scrollContainer).scrollLeft(b(i.scrollContainer).scrollLeft() + e.scroll.moveX);
                                return
                            }
                            var l = b(i.scrollContainer).scrollLeft();
                            if (e.scroll.moveX > 0 && l < e.scroll.maxX || e.scroll.moveX < 0 && l > 0) {
                                b(i.scrollContainer).scrollLeft(l + e.scroll.moveX);
                                e.draggedItem.css("left", e.draggedItem.offset().left + e.scroll.moveX + 1)
                            }
                        }, 10);
                        e.setPos(w.pageX, w.pageY);
                        b(document).bind("selectstart", e.stopBubble);
                        b(document).bind("mousemove", e.swapItems);
                        b(document).bind("mouseup", e.dropItem);
                        if (i.scrollContainer != window) {
                            b(window).bind("DOMMouseScroll mousewheel", e.wheel)
                        }
                        return false
                    },
                    setPos: function(g, r) {
                        var t = r - this.offset.top;
                        var u = g - this.offset.left;
                        if (!i.dragBetween) {
                            t = Math.min(this.offsetLimit.bottom, Math.max(t, this.offsetLimit.top));
                            u = Math.min(this.offsetLimit.right, Math.max(u, this.offsetLimit.left))
                        }
                        this.draggedItem.parents().each(function() {
                            if (b(this).css("position") != "static" && (!b.browser.mozilla || b(this).css("display") != "table")) {
                                var l = b(this).offset();
                                t -= l.top;
                                u -= l.left;
                                return false
                            }
                        });
                        if (i.scrollContainer == window) {
                            r -= b(window).scrollTop();
                            g -= b(window).scrollLeft();
                            r = Math.max(0, r - b(window).height() + 5) + Math.min(0, r - 5);
                            g = Math.max(0, g - b(window).width() + 5) + Math.min(0, g - 5)
                        } else {
                            var k = b(i.scrollContainer);
                            var s = k.offset();
                            r = Math.max(0, r - k.height() - s.top) + Math.min(0, r - s.top);
                            g = Math.max(0, g - k.width() - s.left) + Math.min(0, g - s.left)
                        }
                        e.scroll.moveX = g == 0 ? 0 : g * i.scrollSpeed / Math.abs(g);
                        e.scroll.moveY = r == 0 ? 0 : r * i.scrollSpeed / Math.abs(r);
                        this.draggedItem.css({
                            top: t,
                            left: u
                        })
                    },
                    wheel: function(g) {
                        if ((b.browser.safari || b.browser.mozilla) && e && i.scrollContainer != window) {
                            var k = b(i.scrollContainer);
                            var q = k.offset();
                            if (g.pageX > q.left && g.pageX < q.left + k.width() && g.pageY > q.top && g.pageY < q.top + k.height()) {
                                var p = g.detail ? g.detail * 5 : g.wheelDelta / -2;
                                k.scrollTop(k.scrollTop() + p);
                                g.preventDefault()
                            }
                        }
                    },
                    buildPositionTable: function() {
                        var k = this.draggedItem == null ? null : this.draggedItem.get(0);
                        var g = [];
                        b(this.container).children(i.itemSelector).each(function(q, l) {
                            if (l != k) {
                                var m = b(l).offset();
                                m.right = m.left + b(l).width();
                                m.bottom = m.top + b(l).height();
                                m.elm = l;
                                g.push(m)
                            }
                        });
                        this.pos = g
                    },
                    dropItem: function() {
                        if (e.draggedItem == null) {
                            return
                        }
                        b(e.container).find(i.dragSelector).css("cursor", "pointer");
                        e.placeHolderItem.before(e.draggedItem);
                        var g = e.draggedItem.attr("data-origStyle");
                        if (g == "") {
                            e.draggedItem.removeAttr("style")
                        } else {
                            e.draggedItem.attr("style", g)
                        }
                        e.draggedItem.removeAttr("data-origStyle");
                        e.placeHolderItem.remove();
                        b("[data-dropTarget]").remove();
                        window.clearInterval(e.scroll.scrollY);
                        window.clearInterval(e.scroll.scrollX);
                        var k = false;
                        b(a).each(function() {
                            b(this.container).children(i.itemSelector).each(function(l) {
                                if (parseInt(b(this).attr("data-itemIdx")) != l) {
                                    k = true;
                                    b(this).attr("data-itemIdx", l)
                                }
                            })
                        });
                        if (k) {
                            i.dragEnd.apply(e.draggedItem)
                        }
                        e.draggedItem = null;
                        b(document).unbind("selectstart", e.stopBubble);
                        b(document).unbind("mousemove", e.swapItems);
                        b(document).unbind("mouseup", e.dropItem);
                        if (i.scrollContainer != window) {
                            b(window).unbind("DOMMouseScroll mousewheel", e.wheel)
                        }
                        return false
                    },
                    stopBubble: function() {
                        return false
                    },
                    swapItems: function(p) {
                        if (e.draggedItem == null) {
                            return false
                        }
                        e.setPos(p.pageX, p.pageY);
                        var q = e.findPos(p.pageX, p.pageY);
                        var g = e;
                        for (var k = 0; q == -1 && i.dragBetween && k < a.length; k++) {
                            q = a[k].findPos(p.pageX, p.pageY);
                            g = a[k]
                        }
                        if (q == -1 || b(g.pos[q].elm).attr("data-placeHolder")) {
                            return false
                        }
                        if (h == null || h.top > e.draggedItem.offset().top || h.left > e.draggedItem.offset().left) {
                            b(g.pos[q].elm).before(e.placeHolderItem)
                        } else {
                            b(g.pos[q].elm).after(e.placeHolderItem)
                        }
                        b(a).each(function(l, m) {
                            m.createDropTargets();
                            m.buildPositionTable()
                        });
                        h = e.draggedItem.offset();
                        return false
                    },
                    findPos: function(k, o) {
                        for (var g = 0; g < this.pos.length; g++) {
                            if (this.pos[g].left < k && this.pos[g].right > k && this.pos[g].top < o && this.pos[g].bottom > o) {
                                return g
                            }
                        }
                        return -1
                    },
                    createDropTargets: function() {
                        if (!i.dragBetween) {
                            return
                        }
                        b(a).each(function() {
                            var g = b(this.container).find("[data-placeHolder]");
                            var k = b(this.container).find("[data-dropTarget]");
                            if (g.size() > 0 && k.size() > 0) {
                                k.remove()
                            } else {
                                if (g.size() == 0 && k.size() == 0) {
                                    b(this.container).append(e.placeHolderItem.removeAttr("data-placeHolder").clone().attr("data-dropTarget", true));
                                    e.placeHolderItem.attr("data-placeHolder", true)
                                }
                            }
                        })
                    }
                };
                c.init();
                a.push(c)
            })
        }
        return this
    };
    b.fn.dragsort.defaults = {
        itemSelector: "li",
        dragSelector: "li",
        dragSelectorExclude: "input, textarea, a[href]",
        dragEnd: function() {
        },
        dragBetween: false,
        placeHolderTemplate: "<li>&nbsp;</li>",
        scrollContainer: window,
        scrollSpeed: 5
    }
})(jQuery);
if (typeof (UIinit) == "undefined") {
    UIinit = {}
}
UIinit.Init = function() {
    jQuery(window).bind("lalaMe", UIinit.Me);
    jQuery(window).trigger({
        type: "lalaGetMe",
        error: UIinit.NotLoggedIn
    })
};
UIinit.Me = function(a) {
    jQuery(window).unbind("lalaMe", UIinit.Me.response);
    UIinit.LoggedIn(a.user)
};
UIinit.LoggedIn = function(a) {
    UIinit.UserLeft(a);
    UIinit.SitesLeft();
    UIinit.TopRight(true, a);
    jQuery(window).trigger({
        type: "lalaUIInit",
        user: a
    })
};
UIinit.NotLoggedIn = function() {
    jQuery(window).unbind("lalaMe", UIinit.Me.response);
    UIinit.TopRight(false, null);
    jQuery(window).trigger({
        type: "lalaUIInit",
        user: null
    });
    if (ALLOW_LOGGED_OUT == false) {
        SignIn.AllowClose = false;
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: "sign-in"
        })
    }
};
UIinit.TopRight = function(b, a) {
    $("#top_right").html(Templates.top_right({
        logged_in_user: b,
        user: a
    }))
};
UIinit.UserLeft = function(a) {
    $("#user_nav").prepend(Templates.user_left({
        user: a
    }))
};
UIinit.SitesLeft = function() {
    $("#user_nav").append(Templates.sites_left())
};
UIinit.LeftDrop = function() {
    $("#left").append(Templates.left_drop())
};
jQuery(window).bind("lalaUserLoggedIn", UIinit.Init);
jQuery(document).ready(UIinit.Init);
if (typeof (AudioPlayer) == "undefined") {
    AudioPlayer = {}
}
TANAudioSwfUrl = player_root + "assets/swf/lalaAudio.swf";
AudioPlayer.QueueNumber = null;
AudioPlayer.Audio = null;
AudioPlayerHalfSong = false;
AudioPlayer.CheckIfPlayingTimeout = null;
AudioPlayer.IsLoaded = false;
AudioPlayer.Init = function() {
    var b = document.getElementById("lala_on_page");
    AudioPlayer.Audio = new TANAudio();
    AudioPlayer.AddListeners();
    jQuery(window).trigger({
        type: "lalaAudioCreated",
        audio: AudioPlayer.Audio
    });
    var c = Storage.Get("queueNumber");
    if (c != null) {
    }
    var a = Storage.Get("queue");
    if (a != null) {
        AudioPlayer.List.current = a;
        jQuery(window).trigger({
            type: "lalaNewPlaylist",
            list: AudioPlayer.List.current
        })
    }
};
AudioPlayer.List = {
    current: [],
    replace: function(a) {
        AudioPlayer.List.current = [];
        AudioPlayer.List.current = jQuery.merge([], a.list);
        AudioPlayer.QueueNumber = a.position;
        if (AudioPlayer.Shuffle.selected == true) {
            AudioPlayer.Shuffle.shuffle()
        }
        Storage.Set("queue", AudioPlayer.List.current);
        jQuery(window).trigger({
            type: "lalaNewPlaylist",
            list: AudioPlayer.List.current
        });
        AudioPlayer.Load(AudioPlayer.List.current[AudioPlayer.QueueNumber].url)
    },
    reorder: function(c) {
        var b = c.oldPosition;
        var a = c.newPosition;
        var d = AudioPlayer.List.current.splice(b, 1);
        AudioPlayer.List.current.splice(a, 0, d[0]);
        Storage.Set("queue", AudioPlayer.List.current);
        if (AudioPlayer.QueueNumber >= a) {
            AudioPlayer.QueueNumber++;
            Storage.Set("queueNumber", AudioPlayer.QueueNumber)
        }
        if (AudioPlayer.QueueNumber == b) {
            AudioPlayer.QueueNumber = a;
            Storage.Set("queueNumber", AudioPlayer.QueueNumber)
        }
    },
    love: function(d) {
        if (d.success == true) {
            var f = d.song;
            var a = AudioPlayer.List.current.length;
            for (var c = 0; c < a; c++) {
                var b = AudioPlayer.List.current[c];
                if (b.id == f.id) {
                    b.viewer_love = f.viewer_love
                }
            }
            Storage.Set("queue", AudioPlayer.List.current)
        }
    },
    unLove: function(d) {
        if (d.success == true) {
            var f = d.song;
            var a = AudioPlayer.List.current.length;
            for (var c = 0; c < a; c++) {
                var b = AudioPlayer.List.current[c];
                if (b.id == f.id) {
                    b.viewer_love = f.viewer_love
                }
            }
            Storage.Set("queue", AudioPlayer.List.current)
        }
    },
    add: function(a) {
        var b = a.song;
        AudioPlayer.List.current.push(b);
        Storage.Set("queue", AudioPlayer.List.current)
    },
    remove: function(e) {
        AudioPlayer.List.current.splice(e, 1);
        Storage.Set("queue", AudioPlayer.List.current);
    }
};
AudioPlayer.PlayPause = function() {
    if (AudioPlayer.Audio.paused == true) {
        AudioPlayer.Audio.play()
    } else {
        AudioPlayer.Audio.pause()
    }
};
AudioPlayer.Load = function(a) {
    clearTimeout(AudioPlayer.CheckIfPlayingTimeout);
    AudioPlayerHalfSong = false;
    AudioPlayer.IsLoaded = false;
    jQuery(window).trigger({
        type: "lalaAudioNewSong",
        song: AudioPlayer.List.current[AudioPlayer.QueueNumber],
        queueNumber: AudioPlayer.QueueNumber
    });
    if (a.indexOf("soundcloud.com") != -1) {
        if (a.indexOf("?") == -1) {
            a = a + "?consumer_key=leL50hzZ1H8tAdKCLSCnw"
        } else {
            a = a + "&consumer_key=leL50hzZ1H8tAdKCLSCnw"
        }
    }
    AudioPlayer.Audio.src = a;
    AudioPlayer.Audio.load();
    AudioPlayer.Audio.play();
    AudioPlayer.CheckIfPlayingTimeout = setTimeout(AudioPlayer.CheckIfPlaying, 15000)
};
AudioPlayer.Previous = function() {
    if (AudioPlayer.QueueNumber > 0) {
        AudioPlayer.QueueNumber--;
        AudioPlayer.Load(AudioPlayer.List.current[AudioPlayer.QueueNumber].url)
    }
};
AudioPlayer.Next = function(a) {
    if (AudioPlayer.QueueNumber < AudioPlayer.List.current.length - 1) {
        AudioPlayer.QueueNumber++;
        AudioPlayer.Load(AudioPlayer.List.current[AudioPlayer.QueueNumber].url)
    } else {
        if (AudioPlayer.List.current[AudioPlayer.QueueNumber].type == "radio") {
            AudioPlayer.QueueNumber = 0;
            AudioPlayer.Load(AudioPlayer.List.current[AudioPlayer.QueueNumber].url)

        }
        else if (a && a.type == "ended") {
            AudioPlayer.Events.stop()
        }
    }
};
AudioPlayer.CheckIfPlaying = function() {
    if (AudioPlayer.Audio.paused == false) {
        if (AudioPlayer.Audio.currentTime < 1) {
            AudioPlayer.Next()
        }
    }
};
AudioPlayer.Error = function() {
    AudioPlayer.Events.error(AudioPlayer.List.current[AudioPlayer.QueueNumber]);
    AudioPlayer.Next()
};
AudioPlayer.AddListeners = function() {
    AudioPlayer.Audio.addEventListener("ended", AudioPlayer.Next, false);
    AudioPlayer.Audio.addEventListener("error", AudioPlayer.Error, false);
    AudioPlayer.Audio.addEventListener("play", AudioPlayer.Events.play, false);
    AudioPlayer.Audio.addEventListener("pause", AudioPlayer.Events.pause, false);
    AudioPlayer.Audio.addEventListener("timeupdate", AudioPlayer.TimeUpdate, false);
    AudioPlayer.Audio.addEventListener("canplay", AudioPlayer.Events.canPlay, false);
    try {
        if (navigator.vendor.indexOf("Apple") != -1) {
            AudioPlayer.Audio.addEventListener("timeupdate", AudioPlayer.BeforeEndCheck, false);
            AudioPlayer.Audio.removeEventListener("ended", AudioPlayer.Next, false)
        }
    } catch (a) {
    }
    jQuery("#prev_button").bind("click", AudioPlayer.Previous);
    jQuery("#play_button").bind("click", AudioPlayer.PlayPause);
    jQuery("#next_button").bind("click", AudioPlayer.Next);
    jQuery("#shuffle_button").bind("click", AudioPlayer.Shuffle.click)
};
AudioPlayer.RemoveListeners = function() {
    AudioPlayer.Audio.removeEventListener("ended", AudioPlayer.Next, false);
    AudioPlayer.Audio.removeEventListener("error", AudioPlayer.Error, false);
    AudioPlayer.Audio.removeEventListener("play", AudioPlayer.Events.play, false);
    AudioPlayer.Audio.removeEventListener("pause", AudioPlayer.Events.pause, false);
    AudioPlayer.Audio.removeEventListener("timeupdate", AudioPlayer.TimeUpdate, false);
    AudioPlayer.Audio.removeEventListener("canplay", AudioPlayer.Events.canPlay, false);
    AudioPlayer.Audio.removeEventListener("timeupdate", AudioPlayer.BeforeEndCheck, false)
};
AudioPlayer.Shuffle = {
    selected: false,
    oldList: [],
    oldQueueNumber: 0,
    click: function(a) {
        if (jQuery(this).hasClass("selected") == true) {
            jQuery(this).removeClass("selected");
            AudioPlayer.Shuffle.selected = false;
            if (AudioPlayer.Shuffle.oldList.length > 0) {
                AudioPlayer.Shuffle.unShuffle()
            }
        } else {
            jQuery(this).addClass("selected");
            AudioPlayer.Shuffle.selected = true;
            if (AudioPlayer.List.current.length > 0) {
                AudioPlayer.Shuffle.shuffle()
            }
        }
    },
    shuffle: function() {
        AudioPlayer.Shuffle.oldList = [];
        AudioPlayer.Shuffle.oldList = jQuery.merge([], AudioPlayer.List.current);
        AudioPlayer.Shuffle.oldQueueNumber = AudioPlayer.QueueNumber;
        var a = AudioPlayer.List.current.splice(AudioPlayer.QueueNumber, 1);
        AudioPlayer.List.current = Utils.Shuffle(AudioPlayer.List.current);
        AudioPlayer.List.current.splice(0, 0, a[0]);
        AudioPlayer.QueueNumber = 0;
        Storage.Set("queue", AudioPlayer.List.current);
        jQuery(window).trigger({
            type: "lalaNewPlaylist",
            list: AudioPlayer.List.current
        })
    },
    unShuffle: function() {
        AudioPlayer.QueueNumber = AudioPlayer.List.current[AudioPlayer.QueueNumber].position;
        AudioPlayer.List.current = [];
        AudioPlayer.List.current = jQuery.merge([], AudioPlayer.Shuffle.oldList);
        Storage.Set("queue", AudioPlayer.List.current);
        jQuery(window).trigger({
            type: "lalaNewPlaylist",
            list: AudioPlayer.List.current
        })
    }
};
AudioPlayer.Seek = function(a) {
    AudioPlayer.Audio.currentTime = Math.floor((a.seekLeft / a.progressWidth) * AudioPlayer.Audio.duration)
};
AudioPlayer.Volume = {
    change: function(a) {
        AudioPlayer.Audio.volume = a.volume
    }
};
AudioPlayer.StorageChanged = function(a) {
    switch (a.originalEvent.key) {
        case "queue":
            AudioPlayer.List.current = JSON.parse(a.originalEvent.newValue);
            break;
        case "queueNumber":
            AudioPlayer.QueueNumber = JSON.parse(a.originalEvent.newValue);
            break;
        default:
            break
    }
};
AudioPlayer.QueueNumberChange = function(a) {
    AudioPlayer.QueueNumber = a.position;
    AudioPlayer.Load(AudioPlayer.List.current[AudioPlayer.QueueNumber].url)
};
AudioPlayer.Events = {
    play: function() {
        jQuery(window).trigger({
            type: "lalaAudioPlay",
            paused: AudioPlayer.Audio.paused,
            song: AudioPlayer.List.current[AudioPlayer.QueueNumber],
            queueNumber: AudioPlayer.QueueNumber
        })
    },
    pause: function() {
        jQuery(window).trigger({
            type: "lalaAudioPause",
            paused: AudioPlayer.Audio.paused,
            song: AudioPlayer.List.current[AudioPlayer.QueueNumber],
            queueNumber: AudioPlayer.QueueNumber
        })
    },
    stop: function() {
        jQuery(window).trigger({
            type: "lalaAudioStop"
        })
    },
    newSong: function(a, b) {
        AudioPlayer.QueueNumber = b;
        jQuery(window).trigger({
            type: "lalaAudioNewSong",
            song: a,
            queueNumber: b
        })
    },
    halfSong: function(a) {
        jQuery(window).trigger({
            type: "lalaAudioSongHalf",
            song: a
        })
    },
    error: function(a) {
        jQuery(window).trigger({
            type: "lalaAudioError",
            song: a
        })
    },
    loadStart: function() {
        jQuery(window).trigger({
            type: "lalaAudioLoadStart"
        })
    },
    canPlay: function() {
        AudioPlayer.IsLoaded = true;
        jQuery(window).trigger({
            type: "lalaAudioCanPlay"
        })
    },
    currentSong: function(d, e, a, b, c) {
        AudioPlayer.QueueNumber = e;
        jQuery(window).trigger({
            type: "lalaAudioCurrentSong",
            song: d,
            queueNumber: e,
            paused: a,
            currentTime: b,
            duration: c
        })
    }
};
AudioPlayer.TimeUpdate = function() {
    if (AudioPlayerHalfSong == false) {
        if (this.currentTime / this.duration > 0.5) {
            AudioPlayerHalfSong = true;
            AudioPlayer.Events.halfSong(AudioPlayer.List.current[AudioPlayer.QueueNumber])
        }
    }
};
AudioPlayer.BeforeEndCheck = function() {
    if (this.duration - this.currentTime < 0.5 && AudioPlayer.IsLoaded == true) {
        AudioPlayer.Next()
    }
};
AudioPlayer.Love = {
    keyup: function(a) {
        var b = AudioPlayer.List.current[AudioPlayer.QueueNumber];
        if (b.viewer_love == null) {
            jQuery(window).trigger({
                type: "lalaLoveSong",
                song: b
            })
        } else {
            jQuery(window).trigger({
                type: "lalaUnLoveSong",
                song: b
            })
        }
    }
};
AudioPlayer.Share = {
    keyup: function(a) {
        jQuery(window).trigger({
            type: "lalaShareSong",
            song: AudioPlayer.List.current[AudioPlayer.QueueNumber]
        })
    }
};
AudioPlayer.Scrobble = {
    nowplaying: {
        method: player_root + "more.php?t=now_playing&songid=",
        listener: function(a) {
            var b = a.song;

            if (Utils.HasValue(b.title) == true && Utils.HasValue(b.artist) == true) {
                var u = loggedInUser.user_id;
				//alert(u);
				 AudioPlayer.Scrobble.nowplaying.request(b.title, b.artist, b.album, b.id, u)
				
				
            }
			
        },
        request: function(d, a, b, e, f) {
            if (SCROBBLING_ENABLED == true) {
                var c = Utils.get_cookie("_xsrf");
                jQuery.ajax({
                    url: AudioPlayer.Scrobble.nowplaying.method + e,
                    type: "POST",
                    dataType: "json",
                    data: {
                        title: d,
                        artist: a,
                        album: b,
                        user: f,
                        _xsrf: c
                    },
                    complete: AudioPlayer.Scrobble.nowplaying.response,
                    cache: false
                })
            }
        },
        response: function(b, c) {
            var a = JSON.parse(b.responseText)
        }
    },
    scrobble: {
        method: player_root + "more.php?t=scrobble.php?songid=",
        listener: function(a) {
            var b = a.song;
            if (Utils.HasValue(b.title) == true && Utils.HasValue(b.artist) == true) {
                AudioPlayer.Scrobble.scrobble.request(b.title, b.artist, b.album, b.id)
            }
        },
        request: function(d, a, b, e) {
            if (SCROBBLING_ENABLED == true) {
                var c = Utils.get_cookie("_xsrf");
                jQuery.ajax({
                    url: AudioPlayer.Scrobble.scrobble.method + e,
                    type: "POST",
                    dataType: "json",
                    data: {
                        title: d,
                        artist: a,
                        album: b,
                        _xsrf: c
                    },
                    complete: AudioPlayer.Scrobble.scrobble.response,
                    cache: false
                })
            }
        },
        response: function(b, c) {
            var a = JSON.parse(b.responseText)
        }
    }
};
jQuery(window).bind("lalaNewSongList", AudioPlayer.List.replace);
jQuery(window).bind("lalaAudioSeek", AudioPlayer.Seek);
jQuery(window).bind("lalaVolumeChange", AudioPlayer.Volume.change);
jQuery(window).bind("keyBoardPlayPause", AudioPlayer.PlayPause);
jQuery(window).bind("keyBoardPrevious", AudioPlayer.Previous);
jQuery(window).bind("keyBoardNext", AudioPlayer.Next);
jQuery(window).bind("storage", AudioPlayer.StorageChanged);
jQuery(window).bind("lalaChangeQueueNumber", AudioPlayer.QueueNumberChange);
jQuery(window).bind("keyBoardLove", AudioPlayer.Love.keyup);
jQuery(window).bind("keyBoardShare", AudioPlayer.Share.keyup);
jQuery(window).bind("lalaChangeQueueOrder", AudioPlayer.List.reorder);
jQuery(window).bind("lalaAudioNewSong", AudioPlayer.Scrobble.nowplaying.listener);
jQuery(window).bind("lalaAudioSongHalf", AudioPlayer.Scrobble.scrobble.listener);
jQuery(window).bind("lalaSongLoved", AudioPlayer.List.love);
jQuery(window).bind("lalaSongUnLoved", AudioPlayer.List.unLove);
jQuery(window).bind("lalaQueueSong", AudioPlayer.List.add);
jQuery(document).ready(AudioPlayer.Init);
if (typeof (History) == "undefined") {
    History = {}
}
History.UseHistoryAPI = false;
History.UseHashChange = false;
History.Href = null;
History.OriginalHref = null;
History.Anchor = {
    click: function(c) {
        var a = jQuery(this).attr("href");
        if (a) {
            if (jQuery(this).attr("target") != "_blank") {
                if (jQuery(this).parent("li").hasClass("dropped") == false) {
                    if (History.UseHistoryAPI == true) {
                        History.Href = a.substr(1);
                        history.pushState({
                            href: History.Href
                        }, History.Href, "/" + History.Href);
                        jQuery(window).trigger({
                            type: "lalaHistoryChange",
                            href: History.Href
                        });
                        return false
                    } else {
                        if (History.UseHashChange == true) {
                            History.Href = a.substr(1);
                            location.hash = "!/" + History.Href;
                            return false
                        }
                    }
                    var b = location.protocol + "//" + location.host + "/" + a;
                    window.open(b);
                    return false
                } else {
                    return false
                }
            } else {
                var d = jQuery(this).attr("outbound_type");
                jQuery(window).trigger({
                    type: "lalaOutboundLink",
                    outbound_type: d,
                    href: a
                })
            }
        }
    }
};
History.HashChange = function() {
    History.Href = location.hash.substr(3);
    jQuery(window).trigger({
        type: "lalaHistoryChange",
        href: History.Href
    })
};
History.Popstate = function(a) {
    if (a.originalEvent.state != null) {
        History.Href = a.originalEvent.state.href;
        jQuery(window).trigger({
            type: "lalaHistoryChange",
            href: History.Href
        })
    } else {
    }
};
History.NeedChange = function(a) {
    if (History.UseHistoryAPI == true) {
        History.Href = a.href;
        history.pushState({
            href: History.Href
        }, History.Href, "/" + History.Href);
        jQuery(window).trigger({
            type: "lalaHistoryChange",
            href: History.Href
        })
    } else {
        if (History.UseHashChange == true) {
            History.Href = a.href;
            location.hash = "!/" + History.Href;
            jQuery(window).trigger({
                type: "lalaHistoryChange",
                href: History.Href
            })
        }
    }
};
jQuery(function() {
    History.Href = location.pathname.substr(3);
    if (history.pushState) {
        History.UseHistoryAPI = true;
        jQuery(window).bind("popstate", History.Popstate);
        History.Href = location.hash.substr(3);
        history.pushState({
            href: History.Href
        }, History.Href, "/" + History.Href);
        jQuery(window).trigger({
            type: "lalaHistoryChange",
            href: History.Href
        });
        if (location.hash != "") {
            if (location.hash.indexOf("#!/") != -1) {
                location.href = "/" + location.hash.substr(3)
            }
        }
    }
    if (History.UseHistoryAPI == false) {
        try {
            window.addEventListener("hashchange", History.HashChange, false);
            History.UseHashChange = true;
            if (location.pathname != "/") {
                location.href = "/#!/" + History.Href
            } else {
                History.Href = location.hash.substr(3)
            }
        } catch (a) {
        }
        try {
            window.attachEvent("onhashchange", History.HashChange);
            History.UseHashChange = true;
            if (location.pathname != "/") {
                location.href = "/#!/" + History.Href
            } else {
                History.Href = location.hash.substr(3)
            }
        } catch (a) {
        }
    }
    History.OriginalHref = History.Href
});
History.PageLoad = function() {
    jQuery(window).trigger({
        type: "lalaHistoryChange",
        href: History.OriginalHref
    })
};
jQuery("a").live("click", History.Anchor.click);
jQuery(window).bind("lalaNeedHistoryChange", History.NeedChange);
jQuery(window).bind("lalaUIInit", History.PageLoad);
if (typeof (LeftSelect) == "undefined") {
    LeftSelect = {}
}
LeftSelect.Selected = null;
LeftSelect.LastHistory = null;
LeftSelect.Init = function() {
    var a = Storage.Get("LeftSelect.ShowPlaying.section");
    if (a != null) {
        LeftSelect.ShowPlaying.select(a)
    }
};
LeftSelect.Change = function(a) {
    LeftSelect.UnSelect(LeftSelect.Selected);
    try {
        LeftSelect.LastHistory = a.href;
        if (a.href.indexOf("/followers") != -1) {
            LeftSelect.LastHistory = LeftSelect.LastHistory.substr(0, LeftSelect.LastHistory.indexOf("/followers"))
        }
        if (a.href.indexOf("/following") != -1) {
            LeftSelect.LastHistory = LeftSelect.LastHistory.substr(0, LeftSelect.LastHistory.indexOf("/following"))
        }
        if (a.href.indexOf("/activity") != -1) {
            LeftSelect.LastHistory = LeftSelect.LastHistory.substr(0, LeftSelect.LastHistory.indexOf("/activity"))
        }
        if (a.href.indexOf("/feed") != -1) {
            LeftSelect.LastHistory = LeftSelect.LastHistory.substr(0, LeftSelect.LastHistory.indexOf("/feed"))
        }
        if (a.href.indexOf("/playlist") != -1) {
            LeftSelect.LastHistory = LeftSelect.LastHistory.substr(0, LeftSelect.LastHistory.indexOf("/playlist"))
        }
        if (a.href.indexOf("explore/") != -1) {
            LeftSelect.LastHistory = "explore"
        }
        if (a.href.indexOf("featured/") != -1) {
            LeftSelect.LastHistory = "featured"
        }
        if (a.href.indexOf("albums/") != -1) {
            LeftSelect.LastHistory = "albums"
        }
        if (a.href.indexOf("artists/") != -1) {
            LeftSelect.LastHistory = "artists"
        }
        //                if (a.href.indexOf("playlist/") != -1) {
        //			LeftSelect.LastHistory = "playlist";
        //		}
        if (a.href.indexOf("playlist/") != -1) {
            LeftSelect.LastHistory = a.href.substring(13);
        }
        if (a.href.indexOf("trending/") != -1) {
            LeftSelect.LastHistory = "trending"
        }
        if (a.href.indexOf("video/") != -1) {
            LeftSelect.LastHistory = "video"
        }
        if (a.href.indexOf("charts/") != -1) {
            LeftSelect.LastHistory = "charts"
        }
        if (jQuery("#left_row_" + LeftSelect.LastHistory).length > 0) {
            LeftSelect.Select(jQuery("#left_row_" + LeftSelect.LastHistory))
        }
    } catch (a) {
    }
};
LeftSelect.Click = function(b) {
    var a = jQuery(this).attr("href");
    jQuery(window).trigger({
        type: "lalaLeftSelectChange",
        href: a
    })
};
LeftSelect.Select = function(a) {
    jQuery(a).addClass("selected");
    LeftSelect.Selected = a
};
LeftSelect.UnSelect = function(a) {
    jQuery(a).removeClass("selected")
};
LeftSelect.ShowPlaying = {
    section: null,
    determine: function(b) {
        LeftSelect.ShowPlaying.unselect();
        var a = b.section;
        if (b.section == "user") {
            a = b.list[b.position].user_love.username
        }
        if (b.section == "you") {
            a = loggedInUser.username
        }
        if (b.section.indexOf("explore_") != -1) {
            a = "explore"
        }
        if (b.section.indexOf("featured_") != -1) {
            a = "feature"
        }
        if (jQuery("#left_row_" + a).length > 0) {
            LeftSelect.ShowPlaying.select(a)
        }
    },
    select: function(a) {
        jQuery("#left_row_" + a).addClass("playing");
        LeftSelect.ShowPlaying.section = a;
        Storage.Set("LeftSelect.ShowPlaying.section", a)
    },
    unselect: function() {
        jQuery("#left_row_" + LeftSelect.ShowPlaying.section).removeClass("playing")
    },
    clear: function() {
        LeftSelect.ShowPlaying.unselect();
        Storage.Remove("LeftSelect.ShowPlaying.section")
    }
};
LeftSelect.Rebuilt = function() {
    try {
        if (jQuery("#left_row_" + LeftSelect.LastHistory).length > 0) {
            LeftSelect.Select(jQuery("#left_row_" + LeftSelect.LastHistory))
        }
    } catch (a) {
    }
};
LeftSelect.RefreshAvatar = function() {
    jQuery("#left_row_logged_in_user_icon > img").attr("src", loggedInUser.image.small + "?" + Math.random())
};
jQuery(".left_row").live("click", LeftSelect.Click);
jQuery(window).bind("lalaHistoryChange", LeftSelect.Change);
jQuery(window).bind("lalaNewSongList", LeftSelect.ShowPlaying.determine);
jQuery(window).bind("lalaAudioStop", LeftSelect.ShowPlaying.clear);
jQuery(window).bind("lalaLeftSelectBuilt", LeftSelect.Rebuilt);
jQuery(window).bind("lalaAvatarSet", LeftSelect.RefreshAvatar);
jQuery(document).ready(LeftSelect.Init);
if (typeof (LeftDrop) == "undefined") {
    LeftDrop = {}
}
LeftDrop.Init = function() {
    var a = document.getElementById("left");
    try {
        a.addEventListener("dragenter", LeftDrop.DragEnter, false);
        a.addEventListener("dragover", LeftDrop.DragOver, false);
        a.addEventListener("dragleave", LeftDrop.DragLeave, false);
        a.addEventListener("drop", LeftDrop.Drop, false)
    } catch (b) {
    }
};
LeftDrop.AttachDragEvents = function(b) {
    if (b.id) {
        var a = document.getElementById(b.id)
    }
    if (b.el) {
        a = b.el
    }
    try {
        a.addEventListener("dragstart", LeftDrop.DragStart, false)
    } catch (b) {
    }
};
LeftDrop.DragEnter = function(a) {
    a.preventDefault();
    a.stopPropagation()
};
LeftDrop.DragOver = function(a) {
    a.preventDefault();
    a.stopPropagation()
};
LeftDrop.DragLeave = function(a) {
    a.preventDefault();
    a.stopPropagation()
};
LeftDrop.Drop = function(f) {
    f.preventDefault();
    f.stopPropagation();
    try {
        f.dataTransfer.dropEffect = "copy";
        var b = f.dataTransfer.getData("text/plain");
        var d = JSON.parse(b);
        var a = jQuery("#left_drop .left_row").length;
        var c = Templates.left_shortcut({
            username: d.left_text,
            position: a
        });
        $("#left_drop").append(c);
        $(window).trigger({
            type: "lalaShortcutAdd",
            username: d.left_text
        })
    } catch (g) {
    }
};
LeftDrop.DragStart = function(h) {
    try {
        var c = jQuery(this).attr("href");
        var d = jQuery(this).attr("drag_img");
        var a = jQuery(this).attr("left_img");
        var b = jQuery(this).attr("left_text");
        var f = {
            left_text: b,
            href: c,
            img: a
        };
        var j = JSON.stringify(f);
        h.dataTransfer.effectAllowed = "copyLink";
        h.dataTransfer.setData("text/plain", j);
        var g = document.createElement("img");
        g.src = d;
        h.dataTransfer.setDragImage(g, 0, 0)
    } catch (i) {
    }
};
if (typeof (LeftShortcuts) == "undefined") {
    LeftShortcuts = {}
}
LeftShortcuts.Shortcuts = [];
LeftShortcuts.Init = function(a) {
    if (a.user != null) {
        LeftShortcuts.Get.request()
    }
};
LeftShortcuts.Get = {
    method: player_root + "more.php&t=settings&action=shortcuts&username=user",
    request: function() {
        var a = Utils.get_cookie("_xsrf");
        jQuery.ajax({
            url: LeftShortcuts.Get.method,
            type: "GET",
            dataType: "json",
            data: {
                _xsrf: a
            },
            complete: LeftShortcuts.Get.response,
            cache: false
        })
    },
    response: function(b, c) {
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                LeftShortcuts.Shortcuts = a.users;
                LeftShortcuts.Build(LeftShortcuts.Shortcuts)
            }
        }
    }
};
LeftShortcuts.Build = function(d) {
    var a = d.length;
    var c = "";
    for (var b = 0; b < a; b++) {
        c += Templates.left_shortcut({
            username: d[b],
            position: b
        })
    }
    jQuery("#left_drop").html(c);
    jQuery("#left_drop").dragsort({
        dragEnd: LeftShortcuts.Drag.end,
        dragSelectorExclude: ".left_row_text, .left_row_icon, .left_row_icon > img, .left_row_status"
    });
    jQuery(window).trigger({
        type: "lalaLeftSelectBuilt"
    })
};
LeftShortcuts.Drag = {
    end: function() {
        jQuery(this).addClass("dropped");
        setTimeout(LeftShortcuts.Drag.removeClass, 100, this);
        var c = jQuery(this).attr("username");
        var b = parseInt(jQuery(this).attr("position"));
        var a = parseInt(jQuery(this).attr("data-itemidx"));
        if (a > b) {
            LeftShortcuts.Move.request(c, null, LeftShortcuts.Shortcuts[a])
        } else {
            LeftShortcuts.Move.request(c, LeftShortcuts.Shortcuts[a], null)
        }
        jQuery("#left_drop > li").each(function(d, e) {
            jQuery(e).attr("position", d)
        })
    },
    removeClass: function(a) {
        jQuery(a).removeClass("dropped")
    }
};
LeftShortcuts.Add = {
    method: player_root + "more.php&t=settings&action=shortcuts&subaction=add",
    listener: function(a) {
        LeftShortcuts.Add.request(a.username, a.before, a.after)
    },
    request: function(e, b, c) {
        var a = Utils.get_cookie("_xsrf");
        var d = LeftShortcuts.Add.method + e;
        if (b) {
            d = LeftShortcuts.Add.method + e + "/before/" + b
        }
        if (c) {
            d = LeftShortcuts.Add.method + e + "/after/" + c
        }
        jQuery.ajax({
            url: d,
            type: "POST",
            dataType: "json",
            data: {
                _xsrf: a
            },
            complete: LeftShortcuts.Add.response,
            cache: false
        })
    },
    response: function(b, c) {
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                LeftShortcuts.Shortcuts = a.users
            }
        }
    }
};
LeftShortcuts.Move = {
    method: player_root + "/settings/shortcuts/user/move/",
    request: function(e, b, c) {
        var a = Utils.get_cookie("_xsrf");
        var d = LeftShortcuts.Move.method + e;
        if (b) {
            d = LeftShortcuts.Move.method + e + "/before/" + b
        }
        if (c) {
            d = LeftShortcuts.Move.method + e + "/after/" + c
        }
        jQuery.ajax({
            url: d,
            type: "POST",
            dataType: "json",
            data: {
                _xsrf: a
            },
            complete: LeftShortcuts.Move.response,
            cache: false
        })
    },
    response: function(b, c) {
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                LeftShortcuts.Shortcuts = a.users
            }
        }
    }
};
jQuery(window).bind("lalaShortcutAdd", LeftShortcuts.Add.listener);
(function() {
    var a = Backbone.View.extend({
        el: $("#left"),
        /*initialize: function () {
         this.model = this.options.model;
         this.user = this.options.user;
         $(this.el).append('<div id="following_list_header" class="left_header active border_right">&nbsp;&nbsp;Following<span class="following_list_count">' + this.user.total_following + '</span></div><!--<div id="playlist_list_header" class="left_header">&nbsp;&nbsp;&#x25b6;lists<span class="playlist_list_count">' + this.user.total_playlist + '</span></div>--><ul id="left_following"></ul><ul id="left_playlist" style="display:none"></ul>');
         _.bindAll(this, "render");
         window.LeftFollowingCollection = new UserFollowingCollection({
         username: this.user.username,
         start: 0,
         results: 500
         });
         window.LeftFollowingCollection.bind("reset", this.followingRender);
         window.LeftFollowingCollection.bind("add", this.addUser);
         window.LeftFollowingCollection.bind("remove", this.removeUser);
         window.LeftFollowingCollection.fetch();
         
         window.LeftPlaylistCollection = new UserPlaylistCollection({
         username: this.user.username,
         start: 0,
         results: 500
         });
         window.LeftPlaylistCollection.bind("reset", this.playlistRender);
         window.LeftPlaylistCollection.bind("add", this.addPlaylist);
         window.LeftPlaylistCollection.bind("remove", this.removePlaylist);
         window.LeftPlaylistCollection.fetch();
         },
         followingRender: function () {
         $("#left_following").html(window.LeftFollowingCollection.map(function (c, d) {
         var b = new LeftUserView({
         model: c
         });
         b.render();
         return b.el
         }));
         $("#left_following").append('<li><a id="left_find_friends" href="/settings/social"><span id="left_find_friends_icon"></span>Find More Friends</a></li>');
         jQuery("#following_list_header").click(function(){
         jQuery(this).addClass("active");
         jQuery("#playlist_list_header").removeClass("active");
         jQuery("#left_following").show();
         jQuery("#left_playlist").hide();
         });
         jQuery(window).trigger({
         type: "lalaLeftSelectBuilt"
         });
         return this
         },
         playlistRender: function () {
         $("#left_playlist").html(window.LeftPlaylistCollection.map(function (c, d) {
         var b = new LeftUserPlaylist({
         model: c
         });
         b.render();
         return b.el
         }));
         jQuery("#playlist_list_header").click(function(){
         jQuery(this).addClass("active");
         jQuery("#following_list_header").removeClass("active");
         jQuery("#left_playlist").show();
         jQuery("#left_following").hide();
         });
         jQuery(window).trigger({
         type: "lalaLeftSelectBuilt"
         });
         return this
         },*/
        addUser: function(b) {
            $("#left_following").prepend(new LeftUserView({
                model: b
            }).render().el);
            jQuery(window).trigger({
                type: "lalaLeftSelectBuilt"
            });
            return this
        },
        removeUser: function(b) {
            b.view.remove();
            jQuery(window).trigger({
                type: "lalaLeftSelectBuilt"
            });
            return this
        },
        addPlaylist: function(b) {
            $("#left_playlist").prepend(new LeftUserPlaylist({
                model: b
            }).render().el);
            jQuery(window).trigger({
                type: "lalaLeftSelectBuilt"
            });
            return this
        },
        removePlaylist: function(b) {
            b.view.remove();
            jQuery(window).trigger({
                type: "lalaLeftSelectBuilt"
            });
            return this
        }
    });
    jQuery(window).bind("lalaUIInit", function(b) {
        if (b.user != null) {
            window.LeftFollowing = new a({
                model: User,
                user: b.user
            })
        }
    });
    jQuery(window).bind("lalaUserFollow", function(d) {
        window.LeftFollowingCollection.add(d.user);
        var b = parseInt(jQuery(".following_list_count").text());
        var c = b + 1;
        jQuery(".following_list_count").text(c);
        jQuery("#song_tab_number_following").text(c)
    });
    jQuery(window).bind("lalaUserUnFollow", function(f) {
        var c = window.LeftFollowingCollection.get(f.user.username);
        window.LeftFollowingCollection.remove(c);
        var b = parseInt(jQuery(".following_list_count").text());
        var d = b - 1;
        jQuery(".following_list_count").text(d);
        jQuery("#song_tab_number_following").text(d)
    });
    jQuery(window).bind("lalaUserFavourite", function(d) {
        window.LeftFollowingCollection.add(d.user);
        var b = parseInt(jQuery(".favourite_list_count").text());
        var c = b + 1;
        jQuery(".favourite_list_count").text(c);
        jQuery("#id").text(c)
    });
    jQuery(window).bind("lalaUserUnlike", function(f) {
        var c = window.LeftFollowingCollection.get(f.user.user_id);
        window.LeftFollowingCollection.remove(c);
        var b = parseInt(jQuery(".following_list_count").text());
        var d = b - 1;
        jQuery(".unlike_list_count").text(d);
        jQuery("#id").text(d)
    });

    jQuery(window).bind("lalaUserPlaylistlike", function(f) {
        var c = window.LeftFollowingCollection.get(f.user.user_id);
        window.LeftFollowingCollection.remove(c);
        var b = parseInt(jQuery(".following_list_count").text());
        var d = b - 1;
        jQuery(".like_list_count").text(d);
        jQuery("#id").text(d)
    });
    jQuery(window).bind("lalaUserPlaylistUnlike", function(f) {
        var c = window.LeftFollowingCollection.get(f.user.user_id);
        window.LeftFollowingCollection.remove(c);
        var b = parseInt(jQuery(".following_list_count").text());
        var d = b - 1;
        jQuery(".unlike_list_count").text(d);
        jQuery("#id").text(d)
    });

    jQuery(window).bind("lalaUserAddPlaylist", function(d) {
        var b = parseInt(jQuery(".playlist_list_count").text());
        var c = b + 1;
        jQuery(".playlist_list_count").text(c);
        jQuery("#song_tab_number_playlist").text(c)
    });
})();
var UserProfileView = Backbone.View.extend({
    el: $("#song_list"),
    template: Templates.user_profile,
    initialize: function() {
        _.bindAll(this, "render", "error");
        jQuery(".song_tab").removeClass("selected");
        jQuery(".song_tab_" + this.options.kind).addClass("selected");
        $("#right").unbind();
        Utils.HideSections("#song_list");
        Utils.ShowLoading("#item_rows");
        var d = this.model.bind("change", this.render);
		//console.log('here');
		//console.log(this);
		//console.log('end');
        this.model.fetch({
            error: this.error
        })
    },
    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        jQuery("#song_list").removeClass("display_none");
        jQuery(".song_tab").removeClass("selected");
        jQuery(".song_tab_" + this.options.kind).addClass("selected");
        section = "user";
        var h = this.model.get("username");
        try {
            if (h == loggedInUser.username) {
                section = "you"
            }
        } catch (d) {
        }
        if (this.options.kind == "songs") {
            jQuery(".song_tab_songs").addClass("selected");
            var f = new UserLovedView({
                model: Song,
                username: h,
                section: section,
                el: $("item_rows"),
                show_user: false
            })
            
        }
        if (this.options.kind == "following") {
            jQuery(".song_tab_following").addClass("selected");
            var a = new UserFollowingView({
                model: User,
                username: h,
                el: $("#item_rows")
            })
        }
        if (this.options.kind == "followers") {
            jQuery(".song_tab_followers").addClass("selected");
            var c = new UserFollowersView({
                model: User,
                username: h,
                el: $("#item_rows")
            })
        }
        if (this.options.kind == "activity") {
            jQuery(".song_tab_activity").addClass("selected");
            var b = new UserNotificationsListView({
                model: User,
                username: h,
                el: $("#item_rows")
            })
        }
        if (this.options.kind == "feed") {
            jQuery(".song_tab_feed").addClass("selected");
            var g = new UserFeedView({
                model: Song,
                username: h,
                el: $("#item_rows"),
                show_user: true
            })
        
        }
        if (this.options.kind == "playlist") {
            jQuery(".song_tab_playlist").addClass("selected");
            var a = new UserPlaylistView({
                model: User,
                username: h,
                el: $("#item_rows")
            })
        }
       var c = getCookieData("lang");
	 
       Utils.SetUserBackground("#right", this.model.get("background"));

	  if (LANG_ARRAY != null) {
        $.each(LANG_ARRAY, function(key, value) {
            $('[data-translate-text="' + key + '"]').html(value);
            //console.log(key+ ' --> tranlated to --> ' + value);
        });
    } else {
        ChangeLanguage.Build(default_lang);

    }
        return this
    },
    error: function() {
        jQuery(window).trigger({
            type: "lalaShowErrorPage",
            el: this.el
        })
    }
});
/*profile = function() {
    jQuery(window).trigger({
        type: "lalaHistoryChange",
        href: History.OriginalHref
    })
}; */


var UserLovedView = SongListView.extend({
    collectionClass: UserLovedCollection,
    section: "user",
    render: function() {
        Utils.ShowLoading("#item_rows");
        jQuery("#item_rows").removeClass("feed");
        return this
    }
});
var UserFollowingView = UserListView.extend({
    collectionClass: UserFollowingCollection,
    render: function() {
        Utils.ShowLoading("#item_rows");
        return this
    }
});
var UserPlaylistView = PlaylistView.extend({
    collectionClass: UserPlaylistCollection,
    render: function() {
        Utils.ShowLoading("#item_rows");
        return this
    }
});
var UserFollowersView = UserListView.extend({
    collectionClass: UserFollowersCollection,
    render: function() {
        Utils.ShowLoading("#item_rows");
        return this
    }
});
var UserNotificationsView = Backbone.View.extend({
    template: Templates.user_notifications,
    tagName: "div",
    className: "user_notification",
    render: function() {
        $(this.el).html(this.template({
            notification: this.model.toJSON()
        }));
        var a = parseInt(this.model.get("position")) % 2 == 0;
        $(this.el).addClass(a + "");
        return this
    }
});
var UserFeedView = SongListView.extend({
    collectionClass: SongLovedFeedCollection,
    section: "feed",
    render: function() {
        Utils.ShowLoading("#item_rows");
        jQuery("#item_rows").addClass("feed");
        return this
    }
});
var UserNotificationsListView = Backbone.View.extend({
    el: $("#item_rows"),
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render", "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch");
        $("#right").unbind();
        this.notifications = new UserNotificationCollection({
            username: this.username
        });
        this.notifications.bind("add", this.onAdd);
        this.notifications.bind("reset", this.onInitialFetch);
        this.notifications.bind("fetch", this.onFetch);
        this.notifications.fetch();
        $("#right").bind("scroll", Utils.ScrollBottom);
        Utils.ShowLoading("#item_rows")
    },
    onInitialFetch: function(a) {
        $("#right").scrollTop(0);
        $("#item_rows").empty();
        this.notifications.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        var a = new UserNotificationsView({
            model: b
        });
        $("#item_rows").append(a.render().el)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        jQuery("#load_more").remove();
        if (this.notifications.hasMore == true) {
            var a = Templates.list_load_more();
            $("#item_rows").append(a);
            $("#right").bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $("#right").unbind("scrollBottom", this.onScrollBottom);
        if (this.notifications.hasMore) {
            this.notifications.start += this.notifications.results;
            this.notifications.fetch({
                add: true
            })
        }
    }
});
//$('a.Download_songs').click(function(){
//    var src = $(this).attr('id');
//    src = src;
//    alert(src);
//    $.ajax({
//        type: 'POST',
//        data:{
//            file :src
//        },
//        datatype: 'json',
//        url:'/demo123.php',
//        async: true,
//        cache: false
//    })
//});
jQuery(window).bind("lalaHistoryChange", function(d) {
    switch (d.href) {
        case "":
            break;
        case "/":
            break;
        case "feed":
            break;
        case "tastemakers":
            break;
        case "timeline":
            break;
        case "trending":
            break;
        case "history":
            break;
        case "browse":
            break;
        case "settings":
            break;
        case "explore":
            break;
        case "featured":
            break;
        case "help":
            break;
        case "sign-in":
            break;
        case "sign-out":
            break;
        case "create-account":
            break;
        case "tutorial":
            break;
        case "playlist":
            break;
        case "userlist":
            break;
        case "albums":
            break;
        case "artist":
            break;
        case "composer":
            break;
        case "artists":
            break;
        case "newsongs":
            break;
        case "newrelease":
            break;
        case "radio":
            break;
        case "radiomirchi":
            break;
        case "welcome-to-the-new-lala":
            break;
        case "country":
            break;
        case "index.php":
            break;
        case "http://www.paypal.com/cgi-bin/webscr":
            break;
        default:
            if (d.href.indexOf("song/") == -1 && d.href.indexOf("composer/") == -1 && d.href.indexOf("explore/") == -1 && d.href.indexOf("featured/") == -1 && d.href.indexOf("artists/") == -1 && d.href.indexOf("albums/") == -1 && d.href.indexOf("trending/") == -1 && d.href.indexOf("video/") == -1 && d.href.indexOf("playlist/") == -1 && d.href.indexOf("album/") == -1 && d.href.indexOf("artist/") == -1 && d.href.indexOf("settings/") == -1 && d.href.indexOf("search/") == -1 && d.href.indexOf("tutorial/") == -1) {
                var b = "songs";
                var f = d.href;
                if (d.href.indexOf("/followers") != -1) {
                    b = "followers";
                    f = d.href.substr(0, d.href.indexOf("/followers"))
                }
                if (d.href.indexOf("/following") != -1) {
                    b = "following";
                    f = d.href.substr(0, d.href.indexOf("/following"))
                }
                if (d.href.indexOf("/activity") != -1) {
                    b = "activity";
                    f = d.href.substr(0, d.href.indexOf("/activity"))
                }
                if (d.href.indexOf("/feed") != -1) {
                    b = "feed";
                    f = d.href.substr(0, d.href.indexOf("/feed"))
                }
                if (d.href.indexOf("/playlist") != -1) {
                    b = "playlist";
                    f = d.href.substr(0, d.href.indexOf("/playlist"))
                }
                var a = new User({
                    username: f
                });
                var c = new UserProfileView({
                    model: a,
                    kind: b
                })
            }
            break
    }
});
if (typeof (BottomControls) == "undefined") {
    BottomControls = {}
}
BottomControls.ShowPause = function() {
    jQuery("#play_button").addClass("paused")
};
BottomControls.ShowPlay = function() {
    jQuery("#play_button").removeClass("paused")
};
BottomControls.CurrentSong = function(a) {
    if (a.paused == true) {
        BottomControls.ShowPlay()
    } else {
        BottomControls.ShowPause()
    }
};
jQuery(window).bind("lalaAudioStop", BottomControls.ShowPlay);
jQuery(window).bind("lalaAudioPlay", BottomControls.ShowPause);
jQuery(window).bind("lalaAudioPause", BottomControls.ShowPlay);
jQuery(window).bind("lalaAudioCurrentSong", BottomControls.CurrentSong);
if (typeof (BottomDisplay) == "undefined") {
    BottomDisplay = {}
}
BottomDisplay.SeekThumb = null;
BottomDisplay.SeekThumbOffset = 25;
BottomDisplay.ProgressedBar = null;
BottomDisplay.ProgressedBarOffset = 3;
BottomDisplay.ProgressWidth = null;
BottomDisplay.ProgressLeft = null;
BottomDisplay.ProgressRight = null;
BottomDisplay.TimeCount = null;
BottomDisplay.TimeTotal = null;
BottomDisplay.Init = function(a) {
    a.audio.addEventListener("timeupdate", BottomDisplay.TimeUpdate, false);
    BottomDisplay.SeekThumb = jQuery("#display_seek_thumb")[0];
    jQuery(BottomDisplay.SeekThumb).bind("mousedown", BottomDisplay.Seek.mouseDown);
    jQuery(BottomDisplay.SeekThumb).bind("mouseup", BottomDisplay.Seek.mouseUp);
    BottomDisplay.ProgressedBar = jQuery("#display_progressed")[0];
    jQuery(BottomDisplay.ProgressedBar).bind("click", BottomDisplay.Seek.click);
    jQuery("#display_progress").bind("click", BottomDisplay.Seek.click);
    BottomDisplay.Volume.thumb = jQuery("#volume_thumb")[0];
    jQuery(BottomDisplay.Volume.thumb).bind("mousedown", BottomDisplay.Volume.mouseDown);
    jQuery(BottomDisplay.Volume.thumb).bind("mouseup", BottomDisplay.Volume.mouseUp);
    jQuery("#volume_back").bind("click", BottomDisplay.Volume.backClick);
    BottomDisplay.Volume.speaker = jQuery("#volume_speaker")[0];
    jQuery(BottomDisplay.Volume.speaker).bind("click", BottomDisplay.Volume.speakerClick);
    BottomDisplay.TimeCount = jQuery("#display_time_count");
    BottomDisplay.TimeTotal = jQuery("#display_time_total");
    jQuery("#current_song_love_icon").bind("click", BottomDisplay.Love.click);
    jQuery("#current_song_share_icon").bind("click", BottomDisplay.Share.click);
    jQuery("#current_song_lyrics_icon").bind("click", BottomDisplay.Lyrics.click);
    BottomDisplay.Resize()
};
BottomDisplay.Resize = function() {
    BottomDisplay.ProgressWidth = jQuery("#display_progress").width();
    BottomDisplay.ProgressLeft = jQuery("#display_progress").offset().left;
    BottomDisplay.ProgressRight = BottomDisplay.ProgressLeft + BottomDisplay.ProgressWidth;
    BottomDisplay.Volume.width = jQuery("#volume_back").width();
    BottomDisplay.Volume.left = jQuery("#volume_back").offset().left;
    BottomDisplay.Volume.right = BottomDisplay.Volume.left + BottomDisplay.Volume.width;
    var a = jQuery("#display_text").width() - 200;
    jQuery("#display_song").css("maxWidth", a * 0.4);
    jQuery("#display_artist").css("maxWidth", a * 0.3);
    jQuery("#display_album").css("maxWidth", a * 0.3)
};
BottomDisplay.NewSong = function(b, a) {
    var c = b.song;
    if (c != undefined) {
        BottomDisplay.WhenPlaying();
        BottomDisplay.Resize();
        if (a == null) {
            BottomDisplay.ShowLoading()
        }
        BottomDisplay.ShowText(c);
        BottomDisplay.ShowCoverArt(c);
        BottomDisplay.Love.show(c)
    }
};
BottomDisplay.CurrentSong = function(a) {
    var b = a.song;
    if (b != undefined) {
        BottomDisplay.WhenPlaying();
        BottomDisplay.Resize();
        BottomDisplay.ShowText(b);
        BottomDisplay.ShowCoverArt(b);
        BottomDisplay.Love.show(b)
    }
};

BottomDisplay.WhenPlaying = function() {
    jQuery(".hide_when_playing").addClass("display_none");
    jQuery(".hide_when_stopped").removeClass("display_none")
};
BottomDisplay.WhenStopped = function() {
    jQuery(".hide_when_stopped").addClass("display_none");
    jQuery(".hide_when_playing").removeClass("display_none")
};
BottomDisplay.Stop = function(a) {
    BottomDisplay.WhenStopped()
};
BottomDisplay.Love = {
    show: function(a) {
        if (a.viewer_love != null) {
            jQuery("#current_song_love_icon").addClass("on");
            jQuery("#current_song_love_icon").attr("tooltip", "Unlike")
        } else {
            jQuery("#current_song_love_icon").removeClass("on");
            jQuery("#current_song_love_icon").attr("tooltip", "Love this song")
        }
        jQuery("#current_song_love_icon").addClass("current_song_love_icon_" + a.id)
    },
    click: function(a) {
        jQuery(this).addClass("loading");
        if (jQuery(this).hasClass("on") == true) {
            jQuery(window).trigger({
                type: "lalaUnLoveSong",
                song: AudioPlayer.List.current[AudioPlayer.QueueNumber]
            })
        } else {
            jQuery(window).trigger({
                type: "lalaLoveSong",
                song: AudioPlayer.List.current[AudioPlayer.QueueNumber]
            })
        }
    },
    addOn: function(a) {
        jQuery(".current_song_love_icon_" + a.song.id).removeClass("loading");
        if (a.success == true) {
            jQuery(".current_song_love_icon_" + a.song.id).addClass("on")
        }
    },
    removeOn: function(a) {
        jQuery(".current_song_love_icon_" + a.song.id).removeClass("loading");
        if (a.success == true) {
            jQuery(".current_song_love_icon_" + a.song.id).removeClass("on")
        }
    }
};
BottomDisplay.Share = {
    click: function(a) {
        jQuery(window).trigger({
            type: "lalaShareSong",
            song: AudioPlayer.List.current[AudioPlayer.QueueNumber]
        })
    }
};
BottomDisplay.Lyrics = {
    click: function(a) {
        var current_song = AudioPlayer.List.current[AudioPlayer.QueueNumber];
        jQuery("#lyrics_box_title").html(current_song.title + ' lyrics');
        jQuery.ajax({
            url: "/more.php?t=lyrics",
            type: "GET",
            dataType: "json",
            data: {
                song_id: current_song.id
            },
            complete: function(b, d) {
                var c = JSON.parse(b.responseText);
                if (c.lyrics != null && c.lyrics) {
                    jQuery("#lyrics_box_content").html(c.lyrics);
                } else {
                    jQuery("#lyrics_box_content").html("No lyrics");
                }
                jQuery("#full_cover").removeClass("display_none");
                jQuery("#lyrics_box").removeClass("display_none");
            },
            cache: false
        });
        jQuery("#lyrics_box_close_button").click(function() {
            jQuery("#full_cover").addClass("display_none");
            jQuery("#lyrics_box").addClass("display_none");
        });
    }
};
BottomDisplay.ShowText = function(a) {
    //console.log(a);
    jQuery("#display_song").html("");
    jQuery("#display_song").removeAttr("href");
    jQuery("#display_artist").html("");
    jQuery("#display_artist").removeAttr("href");
    jQuery("#display_album").html("");
    jQuery("#display_domain").html("");
    jQuery("#display_domain").removeAttr("href");
    jQuery("#display_song").html(Utils.BlankUndefined(a.title));
    jQuery("#display_song").attr("href", "/song/" + a.id);
    jQuery("#display_artist").html(Utils.BlankUndefined(a.artist));
    jQuery("#display_album").html(Utils.BlankUndefined(a.album));

    jQuery(".Download_songs").attr('id', a.id);
    jQuery(".Download_songs").attr('data-title', a.title);
    jQuery(".Download_songs").attr('data-album', a.album);
    jQuery(".Download_songs").attr('data-album_id', a.album_id);
//    console.log(a.album);
    jQuery(".Download_songs").attr('data-image', a.image.small);

    jQuery("#display_domain").attr("href", a.source)
};
BottomDisplay.ShowCoverArt = function(a) {
    jQuery(".currentartpic").css("background", "");
    jQuery(".currentartpic").css("background", Utils.CoverArt(a.image.extralarge, 45));
    jQuery(".currentartpic").css("background-size", "cover");
    jQuery(".currentartpic").css("background-position", "10px");
    jQuery(".currentartpic").css("-webkit-filter", "blur(35px)");
    jQuery(".currentartpic").css("filter", "url('#svgBlur')");
    jQuery("#display_coverart").css("background", "");
    jQuery("#display_coverart").css("background", Utils.CoverArt(a.image.small, 45));
    jQuery("#display_coverart").css("background-size", "cover");
    jQuery("#display_coverart_user").addClass("display_none");

};
BottomDisplay.ShowLoading = function() {
    jQuery(BottomDisplay.SeekThumb).addClass("display_none");
    jQuery(BottomDisplay.SeekThumb).css("left", BottomDisplay.SeekThumbOffset);
    jQuery(BottomDisplay.ProgressedBar).addClass("display_none");
    jQuery(BottomDisplay.ProgressedBar).css("width", 0);
    jQuery("#display_progress").addClass("loading");
    jQuery("#display_time_count").text("0:00");
    jQuery("#display_time_total").text("0:00")
};
BottomDisplay.HideLoading = function() {
    jQuery(BottomDisplay.SeekThumb).removeClass("display_none");
    jQuery(BottomDisplay.ProgressedBar).removeClass("display_none");
    jQuery("#display_progress").removeClass("loading")
};
BottomDisplay.TimeUpdate = function() {
    jQuery(BottomDisplay.TimeCount).text(Utils.MMSS(Math.floor(this.currentTime)));
    jQuery(BottomDisplay.TimeTotal).text(Utils.MMSS(Math.floor(this.duration)));
    var a = this.currentTime / this.duration;
    if (BottomDisplay.Seek.isSeeking == false) {
        if ((BottomDisplay.ProgressWidth * a) > 0) {
            jQuery(BottomDisplay.SeekThumb).css("left", BottomDisplay.ProgressWidth * a + BottomDisplay.SeekThumbOffset)
        }
        jQuery(BottomDisplay.ProgressedBar).css("width", BottomDisplay.ProgressWidth * a + BottomDisplay.ProgressedBarOffset)
    }
};
BottomDisplay.Seek = {
    isSeeking: false,
    thumb: null,
    seconds: null,
    seekLeft: null,
    mouseDown: function(a) {
        jQuery(document).bind("mousemove", BottomDisplay.Seek.mouseMove);
        jQuery(document).bind("mouseup", BottomDisplay.Seek.mouseUp);
        BottomDisplay.Seek.isSeeking = true;
        try {
            a.preventDefault()
        } catch (b) {
        }
    },
    mouseUp: function(a) {
        jQuery(document).unbind("mousemove", BottomDisplay.Seek.mouseMove);
        jQuery(document).unbind("mouseup", BottomDisplay.Seek.mouseUp);
        BottomDisplay.Seek.isSeeking = false;
        jQuery(window).trigger({
            type: "lalaAudioSeek",
            seekLeft: BottomDisplay.Seek.seekLeft,
            progressWidth: BottomDisplay.ProgressWidth
        })
    },
    mouseMove: function(b) {
        var a = b.clientX;
        try {
            if (a < BottomDisplay.ProgressLeft) {
                a = BottomDisplay.ProgressLeft
            }
            if (a > BottomDisplay.ProgressRight) {
                a = BottomDisplay.ProgressRight
            }
            BottomDisplay.Seek.seekLeft = a - BottomDisplay.ProgressLeft;
            jQuery(BottomDisplay.SeekThumb).css("left", seekLeft + 23);
            jQuery(BottomDisplay.ProgressedBar).css("width", BottomDisplay.Seek.seekLeft)
        } catch (b) {
        }
    },
    click: function(a) {
        BottomDisplay.Seek.mouseMove(a);
        jQuery(window).trigger({
            type: "lalaAudioSeek",
            seekLeft: BottomDisplay.Seek.seekLeft,
            progressWidth: BottomDisplay.ProgressWidth
        })
    }
};
BottomDisplay.Volume = {
    volume: 100,
    offset: 0,
    thumb: null,
    left: 0,
    right: 0,
    width: 0,
    speaker: null,
    mouseDown: function(a) {
        jQuery(document).bind("mousemove", BottomDisplay.Volume.mouseMove);
        jQuery(document).bind("mouseup", BottomDisplay.Volume.mouseUp);
        jQuery(BottomDisplay.Volume.thumb).addClass("volume_thumb_active");
        a.preventDefault()
    },
    mouseUp: function(a) {
        jQuery(document).unbind("mousemove", BottomDisplay.Volume.mouseMove);
        jQuery(document).unbind("mouseup", BottomDisplay.Volume.mouseUp);
        jQuery(BottomDisplay.Volume.thumb).removeClass("volume_thumb_active")
    },
    mouseMove: function(b) {
        var a = b.clientX;
        try {
            if (a < BottomDisplay.Volume.left - 3) {
                a = BottomDisplay.Volume.left - 3
            }
            if (a > BottomDisplay.Volume.right - 10) {
                a = BottomDisplay.Volume.right - 10
            }
            var c = a - BottomDisplay.Volume.left;
            BottomDisplay.Volume.set(c);
            BottomDisplay.Volume.volume = c / (BottomDisplay.Volume.width - 10);
            if (BottomDisplay.Volume.volume < 0) {
                BottomDisplay.Volume.volume = 0
            }
            if (BottomDisplay.Volume.volume > 1) {
                BottomDisplay.Volume.volume = 1
            }
            jQuery(window).trigger({
                type: "lalaVolumeChange",
                volume: BottomDisplay.Volume.volume
            })
        } catch (b) {
        }
    },
    set: function(a) {
        jQuery(BottomDisplay.Volume.thumb).css("left", a);
        if (a <= 0) {
            jQuery(BottomDisplay.Volume.speaker).removeClass("volume_on");
            jQuery(BottomDisplay.Volume.speaker).addClass("volume_off")
        } else {
            jQuery(BottomDisplay.Volume.speaker).removeClass("volume_off");
            jQuery(BottomDisplay.Volume.speaker).addClass("volume_on")
        }
    },
    event: function(a) {
        var b = a.volume * (BottomDisplay.Volume.width - 10);
        if (b < BottomDisplay.Volume.offset) {
            b = BottomDisplay.Volume.offset
        }
        lalaProfilePlayer.Volume.set(b)
    },
    backClick: function(a) {
        BottomDisplay.Volume.mouseMove(a)
    },
    speakerClick: function(a) {
        if (jQuery(this).hasClass("volume_on")) {
            BottomDisplay.Volume.mouseMove({
                clientX: 0
            })
        } else {
            BottomDisplay.Volume.mouseMove({
                clientX: 1000
            })
        }
    }
};
jQuery(window).bind("lalaAudioCreated", BottomDisplay.Init);
jQuery(window).bind("lalaAudioNewSong", BottomDisplay.NewSong);
jQuery(window).bind("lalaAudioCurrentSong", BottomDisplay.CurrentSong);
jQuery(window).bind("lalaAudioStop", BottomDisplay.Stop);
jQuery(window).bind("resize", BottomDisplay.Resize);
jQuery(window).bind("lalaAudioCanPlay", BottomDisplay.HideLoading);
jQuery(window).bind("lalaSongLoved", BottomDisplay.Love.addOn);
jQuery(window).bind("lalaSongUnLoved", BottomDisplay.Love.removeOn);
if (typeof (PlaylistContainer) == "undefined") {
    PlaylistContainer = {}
}
PlaylistContainer.IsOpen = false;
PlaylistContainer.List = [];
PlaylistContainer.Init = function() {
    jQuery("#playlist_button").bind("click", PlaylistContainer.Click);
    jQuery("#current_playlist_close").bind("click", PlaylistContainer.Close);
    $("#history_songs").bind("click", PlaylistContainer.history);
    $("#to_queue").bind("click", PlaylistContainer.toqueue);
    $("#current_playlist_clear").bind("click", PlaylistContainer.clearAll)
};
PlaylistContainer.Click = function(a) {
    if (PlaylistContainer.IsOpen == false) {
        PlaylistContainer.Open()
    } else {
        PlaylistContainer.Close()
    }
};
PlaylistContainer.Open = function() {
    if (PlaylistContainer.Build() == true) {
        jQuery("#current_playlist").addClass("open");
        PlaylistContainer.IsOpen = true
    }
};
PlaylistContainer.Close = function() {
    jQuery("#current_playlist").removeClass("open");
    PlaylistContainer.IsOpen = false
};
PlaylistContainer.clearAll = function() {
    jQuery('#current_playlist').removeClass("open");
    PlaylistContainer.IsOpen = false;
    AudioPlayer.List.current = [];
    Storage.Set("queue", AudioPlayer.List.current);
    jQuery(window).trigger({
        "type": "lalaNewPlaylist",
        "list": AudioPlayer.List.current
    });
}
PlaylistContainer.Build = function() {
    PlaylistContainer.List = Storage.Get("queue");
    // alert(PlaylistContainer.List);
    //console.log(PlaylistContainer.List);
    if (PlaylistContainer.List != null) {
        var a = PlaylistContainer.List.length;
        var d = Templates.play_queue;
        var c = "";
        for (var b = 0; b < a; b++) {
            var e = PlaylistContainer.List[b];
            c += d({
                song: e,
                position: b
            })
        }
        jQuery("#current_playlist_rows").html(c);
        PlaylistContainer.PlayButton.select({
            queueNumber: AudioPlayer.QueueNumber
        });
        jQuery("#current_playlist_rows")[0].scrollTop = 40 * AudioPlayer.QueueNumber;
        jQuery("#current_playlist_rows").dragsort({
            dragEnd: PlaylistContainer.Reorder.dragEnd,
            scrollContainer: "#current_playlist_rows",
            scrollSpeed: 8,
            dragSelectorExclude: ".current_playlist_play_button, .current_playlist_songtitle, .current_playlist_artist, .current_playlist_love_icon, .current_playlist_link, .current_playlist_remove_icon"
        })
    }
    return true
};
PlaylistContainer.toqueue = function() {

    //  jQuery("#current_playlist_history").addClass('display_none');
    jQuery("#current_playlist_history").css('display', 'none');
//    //      jQuery("#current_playlist_rows").removeClass('display_none');
    jQuery("#current_playlist_rows").css('display', 'block');
    AudioPlayer.List.current = Storage.Get("queue")
    PlaylistContainer.PlayButton.select({
        queueNumber: AudioPlayer.QueueNumber
    });
    jQuery("#current_playlist_rows")[0].scrollTop = 40 * AudioPlayer.QueueNumber;

}

PlaylistContainer.history = function() {

    jQuery("#current_Playlist").addClass("open");
    PlaylistContainer.Isopen = true;
    PlaylistContainer.history.build.request();

}
PlaylistContainer.history.build = {
    method: player_root + "more.php?t=historysongs",
    request: function() {
        var b = PlaylistContainer.history.build.method;
        var id = loggedInUser.user_id;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                userid: id
            },
            complete: PlaylistContainer.history.build.response,
            cache: false
        })
    },
    response: function(b, d) {
        // alert("success");
        var c = Utils.APIResponse(b, "PlaylistContainer.history.build.response");

        if (c.success == true) {
            //                                      alert(c.json.buffer.songs);
            //                    console.log(c.json.buffer.songs);

            var a = (c.json.buffer.songs.length);
            //  alert(a);
            var d = Templates.history_queue;
            //  alert(d);
            var f = "";
            var song = [];
            for (var b = 0; b < a; b++) {

                var e = c.json.buffer.songs[b];

                song[b] = c.json.buffer.songs[b]

                f += d({
                    song: e,
                    position: b
                })

            }
            //                console.log(song);
            AudioPlayer.List.current = song;

            jQuery("#current_playlist_history").html(f);
            jQuery("#current_playlist_history").css('display', 'block');
            jQuery("#current_playlist_history").removeClass('display_none');
            jQuery("#current_playlist_history").addClass('display');
            jQuery("#current_playlist_rows").css('display', 'none');
            PlaylistContainer.history.PlayButton.select({
                queueNumber: AudioPlayer.QueueNumber
            });
            jQuery("#current_history_rows")[0].scrollTop = 40 * AudioPlayer.QueueNumber;
            jQuery("#current_history_rows").dragsort({
                dragEnd: PlaylistContainer.Reorder.dragEnd,
                scrollContainer: "#current_history_rows",
                scrollSpeed: 8,
                dragSelectorExclude: ".current_history_play_button, .current_history_songtitle, .current_history_artist, .current_playlist_love_icon, .current_history_link,"
            })

        }

    }
}
PlaylistContainer.history.PlayButton = {
    click: function(b) {
        if (!jQuery(this).hasClass("playing")) {
            var a = parseInt(jQuery(this).parent().attr("position"));
            jQuery(window).trigger({
                type: "lalaChangeQueueNumber",
                position: a
            })
        }
    },
    select: function(a) {
        PlaylistContainer.history.PlayButton.unSelect();
        jQuery("#current_history_play_button_" + a.queueNumber).addClass("playing")
    },
    unSelect: function() {
        jQuery(".current_history_play_button").removeClass("playing")
    }
};


PlaylistContainer.OpenClose = function(a) {
    if (PlaylistContainer.IsOpen == true) {
        PlaylistContainer.Close()
    } else {
        PlaylistContainer.Open()
    }
};
PlaylistContainer.Change = function(a) {
    if (PlaylistContainer.IsOpen == true) {
        PlaylistContainer.Build()
    }
};
PlaylistContainer.PlayButton = {
    click: function(b) {
        if (!jQuery(this).hasClass("playing")) {
            var a = parseInt(jQuery(this).parent().attr("position"));
            jQuery(window).trigger({
                type: "lalaChangeQueueNumber",
                position: a
            })
        }
    },
    select: function(a) {
        PlaylistContainer.PlayButton.unSelect();
        jQuery("#current_playlist_play_button_" + a.queueNumber).addClass("playing")
    },
    unSelect: function() {
        jQuery(".current_playlist_play_button").removeClass("playing")
    }
};
PlaylistContainer.Reorder = {
    dragEnd: function() {
        var b = false;
        var a = false;
        jQuery(".current_playlist_row").each(function(d, e) {
            b = !b;
            jQuery(e).removeClass("true false");
            jQuery(e).addClass(b + "");
            var c = parseInt(jQuery(e).attr("position"));
            if (c != d && a == false) {
                a = true;
                jQuery(window).trigger({
                    type: "lalaChangeQueueOrder",
                    oldPosition: c,
                    newPosition: d
                })
            }
            jQuery(e).attr("position", d);
            jQuery(e).find(".current_playlist_play_button").attr("id", "current_playlist_play_button_" + d)
        })
    }
};
PlaylistContainer.LoveIcon = {
    click: function(b) {
        jQuery(this).addClass("loading");
        var a = parseInt(jQuery(this).parent().attr("position"));
        if (jQuery(this).hasClass("on") == true) {
            jQuery(window).trigger({
                type: "lalaUnLoveSong",
                song: PlaylistContainer.List[a]
            })
        } else {
            jQuery(window).trigger({
                type: "lalaLoveSong",
                song: PlaylistContainer.List[a]
            })
        }
    },
    addOn: function(a) {
        jQuery(".current_playlist_love_icon_" + a.song.id).removeClass("loading");
        if (a.success == true) {
            jQuery(".current_playlist_love_icon_" + a.song.id).addClass("on")
        }
    },
    removeOn: function(a) {
        jQuery(".current_playlist_love_icon_" + a.song.id).removeClass("loading");
        if (a.success == true) {
            jQuery(".current_playlist_love_icon_" + a.song.id).removeClass("on")
        }
    }
};
PlaylistContainer.removeIcon = {
    click: function(b) {
        var a = parseInt($(this).parent().attr("position"));
        AudioPlayer.List.remove(a);
        jQuery(window).trigger({
            "type": "lalaNewPlaylist",
            "list": AudioPlayer.List.current
        });
    }
};
PlaylistContainer.StorageChanged = function(a) {
    if (a.originalEvent.key == "queue") {
        PlaylistContainer.Change()
    }
};
jQuery(window).bind("lalaAudioNewSong", PlaylistContainer.PlayButton.select);
jQuery(window).bind("lalaNewPlaylist", PlaylistContainer.Change);
jQuery(".current_playlist_play_button").live("click", PlaylistContainer.PlayButton.click);
jQuery(".current_history_play_button").live("click", PlaylistContainer.PlayButton.click);

jQuery(".current_playlist_love_icon").live("click", PlaylistContainer.LoveIcon.click);
jQuery(".current_playlist_songtitle").live("click", PlaylistContainer.PlayButton.click);
jQuery(".current_history_songtitle").live("click", PlaylistContainer.PlayButton.click);

jQuery(".current_playlist_artist").live("click", PlaylistContainer.Close);
jQuery(".current_playlist_link").live("click", PlaylistContainer.Close);
jQuery(window).bind("lalaSongLoved", PlaylistContainer.LoveIcon.addOn);
jQuery(window).bind("lalaSongUnLoved", PlaylistContainer.LoveIcon.removeOn);
$(".current_playlist_remove_icon").live("click", PlaylistContainer.removeIcon.click);
jQuery(document).ready(PlaylistContainer.Init);
jQuery(window).bind("keyBoardPlayQueue", PlaylistContainer.OpenClose);
jQuery(window).bind("storage", PlaylistContainer.StorageChanged);
jQuery(document).bind("storage", PlaylistContainer.StorageChanged);
if (typeof (FollowUser) == "undefined") {
    FollowUser = {}
}
FollowUser.Click = function(c) {
    var a = jQuery(this).attr("username");
    var b = false;
    jQuery(this).css("opacity", 0.4);
    if (jQuery(this).hasClass("following") == true) {
        b = true
    }
    if (b == true) {
        FollowUser.UnFollow.request(a, this)
    } else {
        FollowUser.Follow.request(a, this)
    }
};
FollowUser.Follow = {
    method: player_root + "more.php?t=profile&username=%user%&action=follow",
    request: function(a, b) {
        if (loggedInUser == null) {
            jQuery(b).css("opacity", 1);
            alert('Please <a href="/sign-in">login</a> to use this feature!', true)
        } else {
            var c = Utils.get_cookie("_xsrf");
            var d = FollowUser.Follow.method.replace("%user%", a);
            jQuery.ajax({
                url: d,
                type: "POST",
                dataType: "json",
                data: {
                    _xsrf: c
                },
                complete: FollowUser.Follow.response,
                context: b
            })
        }
    },
    response: function(b, c) {
        jQuery(this).css("opacity", 1);
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                jQuery(this).addClass("following");
                jQuery(this).removeClass("follow");
                jQuery(this).text("Подписаны");
                jQuery(window).trigger({
                    type: "UserFollow",
                    user: a.user
                })
            } else {
                alert("There was a problem. Please try again.")
            }
        }
    }
};
FollowUser.UnFollow = {
    method: player_root + "more.php?t=profile&username=%user%&action=unfollow",
    request: function(a, b) {
        if (loggedInUser == null) {
            jQuery(b).css("opacity", 1);
            alert("You must be logged in to un-follow people", true)
        } else {
            var c = Utils.get_cookie("_xsrf");
            var d = FollowUser.UnFollow.method.replace("%user%", a);
            jQuery.ajax({
                url: d,
                type: "POST",
                dataType: "json",
                data: {
                    _xsrf: c
                },
                complete: FollowUser.UnFollow.response,
                context: b
            })
        }
    },
    response: function(b, c) {
        jQuery(this).css("opacity", 1);
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                jQuery(this).addClass("follow");
                jQuery(this).removeClass("following");
                jQuery(this).text("Подпишитесь");
                jQuery(window).trigger({
                    type: "lalaUserUnFollow",
                    user: a.user
                })
            } else {
                alert("There was a problem. Please try again.")
            }
        } else {
            alert("There was a problem. Please try again.")
        }
    }
};


jQuery(".follow_button").live("click", FollowUser.Click);

if (typeof (FavouriteArtist) == "undefined") {
    FavouriteArtist = {}
}
FavouriteArtist.Click = function(c) {
    var a = jQuery(this).attr("id");
    //    alert(a);
    var b = false;
    jQuery(this).css("opacity", 0.4);
    if (jQuery(this).hasClass("unlike") == true) {

        b = true
    }
    if (b == true) {
        FavouriteArtist.unlike.request(a, this)
    } else {
        FavouriteArtist.Favourite.request(a, this)
    }
};
FavouriteArtist.Favourite = {
    method: player_root + "more.php?t=favouriteArtist",
    request: function(a, b) {
        var userid = loggedInUser.user_id;
        var c = Utils.get_cookie("_xsrf");
        var d = FavouriteArtist.Favourite.method;
        jQuery.ajax({
            url: d,
            type: "POST",
            dataType: "json",
            data: {
                id: a,
                _xsrf: c,
                userid: userid

            },
            complete: FavouriteArtist.Favourite.response,
            context: b
        })

    },
    response: function(b, c) {
        jQuery(this).css("opacity", 1);
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            //alert (a.user);
            if (a.status_code == 200) {
                jQuery(this).addClass("unlike");
                jQuery(this).removeClass("favourite");
                jQuery(this).text("unlike");
            } else {
                alert("There was a problem. Please try again.")
            }
        }
    }
};
FavouriteArtist.unlike = {
    method: player_root + "more.php?t=unlikeArtist",
    request: function(a, b) {
        //alert (a);
        var userid = loggedInUser.user_id;
        var c = Utils.get_cookie("_xsrf");
        var d = FavouriteArtist.unlike.method;
        jQuery.ajax({
            url: d,
            type: "POST",
            dataType: "json",
            data: {
                id: a,
                _xsrf: c,
                userid: userid

            },
            complete: FavouriteArtist.unlike.response,
            context: b
        })

    },
    response: function(b, c) {
        jQuery(this).css("opacity", 1);
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                jQuery(this).addClass("favourite");
                jQuery(this).removeClass("unlike");
                jQuery(this).text("favourite");

            } else {
                alert("There was a problem. Please try again.")
            }
        }
    }
};
jQuery(".favourite_button").live("click", FavouriteArtist.Click);

if (typeof (LikePlaylist) == "undefined") {
    LikePlaylist = {}
}
LikePlaylist.Click = function(c) {
    var a = jQuery(this).attr("id");
    //    alert(a);
    var b = false;
    jQuery(this).css("opacity", 0.4);
    if (jQuery(this).hasClass("unlike") == true) {

        b = true
    }
    if (b == true) {
        LikePlaylist.unlike.request(a, this)
    } else {
        LikePlaylist.like.request(a, this)
    }
};
LikePlaylist.like = {
    method: player_root + "more.php?t=playlist&action=likeplaylist",
    request: function(a, b) {
        var userid = loggedInUser.user_id;
        var c = Utils.get_cookie("_xsrf");
        var d = LikePlaylist.like.method;
        jQuery.ajax({
            url: d,
            type: "POST",
            dataType: "json",
            data: {
                id: a,
                _xsrf: c,
                userid: userid

            },
            complete: LikePlaylist.like.response,
            context: b
        })

    },
    response: function(b, c) {
        jQuery(this).css("opacity", 1);
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            //            alert (a.user);
            if (a.status_code == 200) {
                jQuery(this).addClass("unlike");
                jQuery(this).removeClass("like");
                jQuery(this).text("unlike");
//                location.reload(); 
            } else {
                alert("There was a problem. Please try again.")
            }
        }
    }
};
LikePlaylist.unlike = {
    method: player_root + "more.php?t=playlist&action=unlikeplaylist",
    request: function(a, b) {
        //alert (a);
        var userid = loggedInUser.user_id;
        var c = Utils.get_cookie("_xsrf");
        var d = LikePlaylist.unlike.method;
        jQuery.ajax({
            url: d,
            type: "POST",
            dataType: "json",
            data: {
                id: a,
                _xsrf: c,
                userid: userid

            },
            complete: LikePlaylist.unlike.response,
            context: b
        })

    },
    response: function(b, c) {
        jQuery(this).css("opacity", 1);
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                jQuery(this).addClass("like");
                jQuery(this).removeClass("unlike");
                jQuery(this).text("like");
//                location.reload();

            } else {
                alert("There was a problem. Please try again.")
            }
        }
    }
};
jQuery(".like_button").live("click", LikePlaylist.Click);

jQuery(".artist_share").live("click", function() {
    if (loggedInUser == null) {

        alert("You must be logged in to share this artist", true)
    } else {
        var e = $(this).attr('data-info')
        //console.log(e);
        var info = e.split(',');

        jQuery(window).trigger({
            type: "lalaShareArtist",
            artist: info
        })
    }
});

if (typeof (ShareArtistBox) == "undefined") {
    ShareArtistBox = {}
}
ShareArtistBox.Listen = function(a) {
    //console.log(a);
    ShareArtistBox.Show(a.artist)
};
ShareArtistBox.InApp = true;
ShareArtistBox.Artist = null;
ShareArtistBox.Value = "";
ShareArtistBox.IsShowing = false;
ShareArtistBox.Show = function(b) {
    if (ShareArtistBox.IsShowing == false) {
        ShareArtistBox.Artist = b;
//        console.log(ShareArtistBox.Artist);
//       console.log (ShareArtistBox.Artist[0]);
        jQuery("#full_cover").removeClass("display_none");
        var a = Templates.shareartist_box({
            shared: false,
            name: ShareArtistBox.Artist[0],
            id: ShareArtistBox.Artist[1]
        });
        jQuery(document.body).append(a);
        ShareArtistBox.AddListeners();
        ShareArtistBox.IsShowing = true
    }
};
ShareArtistBox.Hide = function() {
    jQuery("#share_box_close_button").unbind("click", ShareArtistBox.Hide);
    jQuery("#share_box_textarea").unbind("keyup", ShareArtistBox.TextArea.keyup);
    jQuery("#share_box_icon_twitter").unbind("click", ShareArtistBox.Twitter.click);
    jQuery("#share_box_icon_facebook").unbind("click", ShareArtistBox.Facebook.click);
    jQuery("#share_button").unbind("click", ShareArtistBox.Share.click);
    jQuery("#share_box").remove();
    jQuery("#full_cover").addClass("display_none");
    ShareArtistBox.Service.selected = null;
    ShareArtistBox.Artist = null;
    jQuery(window).trigger({
        type: "lalaShareBoxClose"
    });
    ShareArtistBox.IsShowing = false
};
ShareArtistBox.AddListeners = function() {
    jQuery("#share_box_close_button").bind("click", ShareArtistBox.Hide);
    jQuery("#share_box_icon_twitter").bind("click", ShareArtistBox.Twitter.click);
    jQuery("#share_box_icon_facebook").bind("click", ShareArtistBox.Facebook.click);
    jQuery("#share_button").bind("click", ShareArtistBox.Share.click);
    jQuery("#share_button").removeClass("inactive")
};
ShareArtistBox.TextArea = {
    keyup: function(b) {
        var a = jQuery("#share_box_textarea").attr("value");
        jQuery("#share_box_count").text(140 - a.length)
    }
};
ShareArtistBox.Twitter = {
    message: null,
    tweet: function() {


        return "Listening artist of " + ShareArtistBox.Artist[0] + " on #Nota - http://" + location.host + "/artist/" + ShareArtistBox.Artist[1]
    },
    click: function(b) {
        if (ShareArtistBox.Service.selected != "twitter") {
            ShareArtistBox.Service.removeAll();
            ShareArtistBox.Service.select("twitter");
            ShareArtistBox.Twitter.message = ShareArtistBox.Twitter.tweet();
            var a = jQuery("#share_box_textarea").attr("value");
            if (a != "") {
                ShareArtistBox.Twitter.message = jQuery.trim(a) + " " + ShareArtistBox.Twitter.message
            }
            jQuery("#share_box_textarea").attr("value", ShareArtistBox.Twitter.message);
            jQuery("#share_box_count").text(140 - ShareArtistBox.Twitter.message.length);
            jQuery("#share_box_textarea").bind("keyup", ShareArtistBox.TextArea.keyup)
        }
    }
};
ShareArtistBox.Facebook = {
    message: null,
    click: function(d) {
        if (ShareArtistBox.Service.selected != "facebook") {
            ShareArtistBox.Service.removeAll();
            ShareArtistBox.Service.select("facebook");
            ShareArtistBox.Facebook.message = "";
            var b = jQuery("#share_box_textarea").attr("value");
            var c = b.split(ShareArtistBox.Twitter.tweet());
            for (var a in c) {
                if (c[a] != "") {
                    ShareArtistBox.Facebook.message = jQuery.trim(ShareArtistBox.Facebook.message + " " + jQuery.trim(c[a]))
                }
            }
            jQuery("#share_box_textarea").attr("value", ShareArtistBox.Facebook.message);
            jQuery("#share_box_count").text("");
            jQuery("#share_box_textarea").unbind("keyup", ShareArtistBox.TextArea.keyup)
        }
    }
};
ShareArtistBox.Service = {
    selected: null,
    select: function(a) {
        ShareArtistBox.Service.selected = a;
        jQuery("#share_box_icon_" + a).addClass("selected")
    },
    removeAll: function() {
        jQuery("#share_box_icon_twitter").removeClass("selected");
        jQuery("#share_box_icon_facebook").removeClass("selected")
    }
};
ShareArtistBox.Share = {
    method: player_root + "api/share.php?service=%service%&artistid=%artistid%",
    click: function(a) {
        ShareArtistBox.Value = jQuery("#share_box_textarea").attr("value");
        if (ShareArtistBox.Service.selected != null) {
            jQuery("#share_button").unbind("click", ShareArtistBox.Share.click);
            jQuery("#share_button").addClass("inactive");
            jQuery("#share_box_icon_twitter").unbind("click", ShareArtistBox.Twitter.click);
            jQuery("#share_box_icon_facebook").unbind("click", ShareArtistBox.Facebook.click);
            jQuery("#share_box_middle_error").addClass("display_none");
            ShareArtistBox.Share.request(ShareArtistBox.Value, ShareArtistBox.Artist, ShareArtistBox.Service.selected)
        }
    },
    request: function(b, c, a) {
        var d = ShareArtistBox.Share.method.replace("%service%", a);
        d = d.replace("%artistid%", c[1]);
        b = encodeURIComponent(b);
        b = b.replace("%E2%99%AA", "?");
        if (a == "facebook") {
            window.open("https://www.facebook.com/sharer/sharer.php?u=" + player_root + "artist/" + c[1] + "&t=" + b, "_blank")
        } else {
            if (a == "twitter") {
                window.open("https://twitter.com/intent/tweet?text=" + b, "_blank")
            }
        }
        ShareArtistBox.Hide()
    }
};
ShareArtistBox.Connect = {
    render: function() {
        if (ShareArtistBox.InApp == true) {
            var a = Templates.add_service_modal({
                service: ShareArtistBox.Service.selected
            });
            jQuery("#share_box_middle").html(a);
            jQuery("#share_box_bottom").empty();
            jQuery(".create_account_oauth").bind("click", ShareArtistBox.Connect.click)
        } else {
            jQuery("#share_box_bottom").empty();
            jQuery("#share_box_middle").html('<div id="add_service_modal_middle"><div id="add_service_text">Thanks for sharing this artist! <br />First you must connect with ' + Utils.Capitalize(ShareArtistBox.Service.selected) + '.</div><a id="add_service_lala_link" class="modal_bottom_button generic_button" href="http://nota.kg" target="_blank">Go to Nota</a><div class="clear"></div>')
        }
    },
    click: function() {
        jQuery(".create_account_oauth").unbind("click", ShareArtistBox.Connect.click);
        jQuery(window).bind("lalaServiceConnectionAdd", ShareArtistBox.Connect.listener);
        var a = jQuery(this).attr("service");
        window.open("/connect/" + a);
        Utils.ShowLoading("#add_service_loading")
    },
    listener: function(b) {
        jQuery(window).unbind("lalaServiceConnectionAdd", ShareArtistBox.Connect.listener);
        var a = b.obj;
        if (a.type) {
            ShareArtistBox.Share.request(ShareArtistBox.Value, ShareArtistBox.Artist, ShareArtistBox.Service.selected)
        }
    }
};


jQuery(window).bind("lalaShareArtist", ShareArtistBox.Listen);



jQuery(".album_share").live("click", function() {
    if (loggedInUser == null) {

        alert("You must be logged in to share this album", true)
    } else {
        var e = $(this).attr('data-info')
        //console.log(e);
        var info = e.split(',');

        jQuery(window).trigger({
            type: "lalaShareAlbum",
            album: info
        })
    }
});

if (typeof (ShareAlbumBox) == "undefined") {
    ShareAlbumBox = {}
}
ShareAlbumBox.Listen = function(a) {
    //console.log(a);
    ShareAlbumBox.Show(a.album)
};
ShareAlbumBox.InApp = true;
ShareAlbumBox.Artist = null;
ShareAlbumBox.Value = "";
ShareAlbumBox.IsShowing = false;
ShareAlbumBox.Show = function(b) {
    if (ShareAlbumBox.IsShowing == false) {
        ShareAlbumBox.Album = b;
        jQuery("#full_cover").removeClass("display_none");
        var a = Templates.sharealbum_box({
            shared: false,
            name: ShareAlbumBox.Album[0],
            id: ShareAlbumBox.Album[1],
            artist: ShareAlbumBox.Album[2]
        });
        jQuery(document.body).append(a);
        ShareAlbumBox.AddListeners();
        ShareAlbumBox.IsShowing = true
    }
};
ShareAlbumBox.Hide = function() {
    jQuery("#share_box_close_button").unbind("click", ShareAlbumBox.Hide);
    jQuery("#share_box_textarea").unbind("keyup", ShareAlbumBox.TextArea.keyup);
    jQuery("#share_box_icon_twitter").unbind("click", ShareAlbumBox.Twitter.click);
    jQuery("#share_box_icon_facebook").unbind("click", ShareAlbumBox.Facebook.click);
    jQuery("#share_button").unbind("click", ShareAlbumBox.Share.click);
    jQuery("#share_box").remove();
    jQuery("#full_cover").addClass("display_none");
    ShareAlbumBox.Service.selected = null;
    ShareAlbumBox.Album = null;
    jQuery(window).trigger({
        type: "lalaShareBoxClose"
    });
    ShareAlbumBox.IsShowing = false
};
ShareAlbumBox.AddListeners = function() {
    jQuery("#share_box_close_button").bind("click", ShareAlbumBox.Hide);
    jQuery("#share_box_icon_twitter").bind("click", ShareAlbumBox.Twitter.click);
    jQuery("#share_box_icon_facebook").bind("click", ShareAlbumBox.Facebook.click);
    jQuery("#share_button").bind("click", ShareAlbumBox.Share.click);
    jQuery("#share_button").removeClass("inactive")
};
ShareAlbumBox.TextArea = {
    keyup: function(b) {
        var a = jQuery("#share_box_textarea").attr("value");
        jQuery("#share_box_count").text(140 - a.length)
    }
};
ShareAlbumBox.Twitter = {
    message: null,
    tweet: function() {


        return "Listening album  " + ShareAlbumBox.Album[0] + " of artist " + ShareAlbumBox.Album[2] + " on #Nota - http://" + location.host + "/album/" + ShareAlbumBox.Album[1]
    },
    click: function(b) {
        if (ShareAlbumBox.Service.selected != "twitter") {
            ShareAlbumBox.Service.removeAll();
            ShareAlbumBox.Service.select("twitter");
            ShareAlbumBox.Twitter.message = ShareAlbumBox.Twitter.tweet();
            var a = jQuery("#share_box_textarea").attr("value");
            if (a != "") {
                ShareAlbumBox.Twitter.message = jQuery.trim(a) + " " + ShareAlbumBox.Twitter.message
            }
            jQuery("#share_box_textarea").attr("value", ShareAlbumBox.Twitter.message);
            jQuery("#share_box_count").text(140 - ShareAlbumBox.Twitter.message.length);
            jQuery("#share_box_textarea").bind("keyup", ShareAlbumBox.TextArea.keyup)
        }
    }
};
ShareAlbumBox.Facebook = {
    message: null,
    click: function(d) {
        if (ShareAlbumBox.Service.selected != "facebook") {
            ShareAlbumBox.Service.removeAll();
            ShareAlbumBox.Service.select("facebook");
            ShareAlbumBox.Facebook.message = "";
            var b = jQuery("#share_box_textarea").attr("value");
            var c = b.split(ShareAlbumBox.Twitter.tweet());
            for (var a in c) {
                if (c[a] != "") {
                    ShareAlbumBox.Facebook.message = jQuery.trim(ShareAlbumBox.Facebook.message + " " + jQuery.trim(c[a]))
                }
            }
            jQuery("#share_box_textarea").attr("value", ShareAlbumBox.Facebook.message);
            jQuery("#share_box_count").text("");
            jQuery("#share_box_textarea").unbind("keyup", ShareAlbumBox.TextArea.keyup)
        }
    }
};
ShareAlbumBox.Service = {
    selected: null,
    select: function(a) {
        ShareAlbumBox.Service.selected = a;
        jQuery("#share_box_icon_" + a).addClass("selected")
    },
    removeAll: function() {
        jQuery("#share_box_icon_twitter").removeClass("selected");
        jQuery("#share_box_icon_facebook").removeClass("selected")
    }
};
ShareAlbumBox.Share = {
    method: player_root + "api/share.php?service=%service%&albumid=%albumid%",
    click: function(a) {
        ShareAlbumBox.Value = jQuery("#share_box_textarea").attr("value");
        if (ShareAlbumBox.Service.selected != null) {
            jQuery("#share_button").unbind("click", ShareAlbumBox.Share.click);
            jQuery("#share_button").addClass("inactive");
            jQuery("#share_box_icon_twitter").unbind("click", ShareAlbumBox.Twitter.click);
            jQuery("#share_box_icon_facebook").unbind("click", ShareAlbumBox.Facebook.click);
            jQuery("#share_box_middle_error").addClass("display_none");
            ShareAlbumBox.Share.request(ShareAlbumBox.Value, ShareAlbumBox.Album, ShareAlbumBox.Service.selected)
        }
    },
    request: function(b, c, a) {
        var d = ShareAlbumBox.Share.method.replace("%service%", a);
        d = d.replace("%albumid%", c[1]);
        b = encodeURIComponent(b);
        b = b.replace("%E2%99%AA", "?");
        if (a == "facebook") {
            window.open("https://www.facebook.com/sharer/sharer.php?u=" + player_root + "album/" + c[1] + "&t=" + b, "_blank")
        } else {
            if (a == "twitter") {
                window.open("https://twitter.com/intent/tweet?text=" + b, "_blank")
            }
        }
        ShareAlbumBox.Hide()
    }
};
ShareAlbumBox.Connect = {
    render: function() {
        if (ShareAlbumBox.InApp == true) {
            var a = Templates.add_service_modal({
                service: ShareAlbumBox.Service.selected
            });
            jQuery("#share_box_middle").html(a);
            jQuery("#share_box_bottom").empty();
            jQuery(".create_account_oauth").bind("click", ShareAlbumBox.Connect.click)
        } else {
            jQuery("#share_box_bottom").empty();
            jQuery("#share_box_middle").html('<div id="add_service_modal_middle"><div id="add_service_text">Thanks for sharing this album! <br />First you must connect with ' + Utils.Capitalize(ShareAlbumBox.Service.selected) + '.</div><a id="add_service_lala_link" class="modal_bottom_button generic_button" href="http://nota.kg" target="_blank">Go to Nota</a><div class="clear"></div>')
        }
    },
    click: function() {
        jQuery(".create_account_oauth").unbind("click", ShareAlbumBox.Connect.click);
        jQuery(window).bind("lalaServiceConnectionAdd", ShareAlbumBox.Connect.listener);
        var a = jQuery(this).attr("service");
        window.open("/connect/" + a);
        Utils.ShowLoading("#add_service_loading")
    },
    listener: function(b) {
        jQuery(window).unbind("lalaServiceConnectionAdd", ShareAlbumBox.Connect.listener);
        var a = b.obj;
        if (a.type) {
            ShareAlbumBox.Share.request(ShareAlbumBox.Value, ShareAlbumBox.Album, ShareAlbumBox.Service.selected)
        }
    }
};


jQuery(window).bind("lalaShareAlbum", ShareAlbumBox.Listen);

if (typeof (RemovePlaylist) == "undefined") {
    RemovePlaylist = {}
}
RemovePlaylist.Click = function(c) {
    jQuery(this).css("opacity", 0.4);
    RemovePlaylist.Remove.request(c, this)
};
RemovePlaylist.Remove = {
    method: player_root + "more.php?t=playlist&action=remove&id=%playlist_id%",
    request: function(a, b) {
        if (loggedInUser == null) {
            jQuery(b).css("opacity", 1);
            alert("You must login to use this feature!", true)
        } else {
            var c = Utils.get_cookie("_xsrf");
            var d = RemovePlaylist.Remove.method.replace("%playlist_id%", a);
            jQuery.ajax({
                url: d,
                type: "POST",
                dataType: "json",
                data: {
                    _xsrf: c
                },
                complete: RemovePlaylist.Remove.response,
                context: b
            })
        }
    },
    response: function(b, c) {
        if (b.status == 200) {
            var a = JSON.parse(b.responseText);
            if (a.status_code == 200) {
                $(".display_playlist_" + a.playlist_id).fadeOut();
                $('li[data-playlist-id=' + a.playlist_id + ']').remove();
                var b = parseInt(jQuery(".playlist_list_count").text());
                var c = b - 1;
                jQuery(".playlist_list_count").text(c);
                jQuery("#song_tab_number_playlist").text(c);
                jQuery("#song_tab_number_playlist").text(c);
                $('li[id=' + a.playlist_id + ']').remove()
            } else {
                alert("There was a problem. Please try again.")
            }
        }
    }
};
jQuery(".user_row_playlist_button").live("click", function() {
    var a = jQuery(this).attr("data-playlist-id");
    if (window.confirm("Are you sure?"))
        RemovePlaylist.Click(a);
});

if (typeof (KeyboardShortcuts) == "undefined") {
    KeyboardShortcuts = {}
}
KeyboardShortcuts.Keyup = function(a) {
    if (a.altKey == true) {
        if (a.keyCode == 80) {
            jQuery(window).trigger({
                type: "keyBoardPlayPause"
            })
        }
        if (a.keyCode == 75) {
            jQuery(window).trigger({
                type: "keyBoardNext"
            })
        }
        if (a.keyCode == 74) {
            jQuery(window).trigger({
                type: "keyBoardPrevious"
            })
        }
        if (a.keyCode == 76) {
            jQuery(window).trigger({
                type: "keyBoardLove"
            })
        }
        if (a.keyCode == 83) {
            jQuery(window).trigger({
                type: "keyBoardShare"
            })
        }
        if (a.keyCode == 70) {
            if (!$("#middle").hasClass("collapsed")) {
                $("#middle").addClass("collapsed")
            } else {
                $("#middle").removeClass("collapsed")
            }
        }
        if (a.keyCode == 81) {
            jQuery(window).trigger({
                type: "keyBoardPlayQueue"
            })
        }
    }
};
jQuery(document).bind("keyup", KeyboardShortcuts.Keyup);
if (typeof (Tooltip) == "undefined") {
    Tooltip = {}
}
Tooltip.Timeout = null;
Tooltip.Mouseover = function(h) {
    clearTimeout(Tooltip.Timeout);
    var j = $(this);
    var d = j.offset();
    var p = j.attr("tooltip") || j.find("span").html();
    var b = d.left;
    var m = d.top + 3;
    var a = j.outerWidth();
    var g = jQuery("#tooltip_display");
    g.html(p);
    var f = g.outerWidth();
    var c = b + (a / 2) - f / 2;
    g.css({
        left: c,
        top: m - 30,
        right: "auto"
    });
    g.addClass("show");
    var n = g.offset().left;
    var o = n + f;
    var k = jQuery(document).width();
    if ((o + 30) > k) {
        g.css({
            right: 30,
            left: "auto"
        })
    }
    if (n < 0) {
        g.css({
            left: 10,
            right: "auto"
        })
    }
    var i = g.offset().top;
    if (i < 0) {
        g.css({
            top: 45
        })
    }
    Tooltip.Timeout = setTimeout(Tooltip.Mouseout, 2000);
    j.bind("mouseout", Tooltip.Mouseout)
};
Tooltip.Mouseout = function(a) {
    jQuery("#tooltip_display").removeClass("show")
};
jQuery(".tooltip, .song_row_recent_loves_avatars, .song_page_recent_loves_avatars").live("mouseover", Tooltip.Mouseover);
jQuery(".tooltip").live("mouseout", Tooltip.Mouseout);
jQuery(window).bind("lalaHistoryChange", Tooltip.Mouseout);
if (typeof (LoveSong) == "undefined") {
    LoveSong = {}
}
LoveSong.Love = {
    method: player_root + "more.php?t=love&action=love&songid=%id%",
    request: function(c) {
        var d = c.song;
        if (loggedInUser == null) {
            LoveSong.Love.trigger(d, false);
            alert('<a href="/sign-in">Login</a> to Love this song.', true)
        } else {
            var a = {
                client_id: "lala_web"
            };
            if (d.user_love) {
                if (d.user_love.username != loggedInUser.username) {
                    a.context = d.user_love.username
                }
                a.source = d.user_love.source
            } else {
                a.source = d.source
            }
            var b = Utils.get_cookie("_xsrf");
            var f = LoveSong.Love.method.replace("%id%", d.id);
            jQuery.ajax({
                url: f,
                type: "POST",
                dataType: "json",
                data: a,
                complete: LoveSong.Love.response,
                context: {
                    song: d
                }
            })
        }
    },
    response: function(a, d) {
        var b = Utils.APIResponse(a, "LoveSong.Love.response");
        var c = b.json.song || this.song;
        if (b.success == true) {
            LoveSong.Love.trigger(c, true);
            LoveSong.Love.updateLocalStorage(c)
        } else {
            if (b.json.status_text == "Already loved this song") {
                LoveSong.Love.trigger(c, true);
                LoveSong.Love.updateLocalStorage(c)
            } else {
                LoveSong.Love.trigger(c, false);
                alert("There was a problem. Please try again.")
            }
        }
    },
    trigger: function(b, a) {
        jQuery(window).trigger({
            type: "lalaSongLoved",
            song: b,
            success: a
        })
    },
    updateLocalStorage: function(f) {
        var e = Storage.Get("sitesSongs");
        if (e != null) {
            for (var c in e) {
                var d = e[c];
                if (d.id == f.id) {
                    d.viewer_love = f.viewer_love
                }
            }
            Storage.Set("sitesSongs", e)
        }
        var a = Storage.Get("queue");
        if (a != null) {
            var b = a.length;
            for (var c = 0; c < b; c++) {
                var d = a[c];
                if (d.id == f.id) {
                    d.viewer_love = f.viewer_love
                }
            }
            Storage.Set("queue", a)
        }
    }
};
LoveSong.UnLove = {
    method: player_root + "more.php?t=love&action=unlove&songid=%id%",
    request: function(b) {
        var c = b.song;
        if (loggedInUser == null) {
            LoveSong.UnLove.trigger(c, false);
            alert('Please <a href="/sign-in">login</a> to un-love songs', true)
        } else {
            var d = LoveSong.UnLove.method.replace("%id%", c.id);
            var a = Utils.get_cookie("_xsrf");
            jQuery.ajax({
                url: d,
                type: "POST",
                dataType: "json",
                complete: LoveSong.UnLove.response,
                context: {
                    song: c
                }
            })
        }
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "LoveSong.UnLove.response");
        var a = c.json.song || this.song;
        if (c.success == true) {
            LoveSong.UnLove.trigger(c.json.song, true);
            LoveSong.UnLove.updateLocalStorage(a)
        } else {
            if (c.json.status_text == "Havent loved this song") {
                LoveSong.UnLove.trigger(a, true);
                LoveSong.UnLove.updateLocalStorage(a)
            } else {
                LoveSong.UnLove.trigger(this.song, false);
                alert("There was a problem. Please try again.")
            }
        }
    },
    trigger: function(b, a) {
        jQuery(window).trigger({
            type: "lalaSongUnLoved",
            song: b,
            success: a
        })
    },
    updateLocalStorage: function(d) {
        var f = Storage.Get("sitesSongs");
        if (f != null) {
            for (var c in f) {
                var e = f[c];
                if (e.id == d.id) {
                    e.viewer_love = d.viewer_love
                }
            }
            Storage.Set("sitesSongs", f)
        }
        if (a != null) {
            var a = Storage.Get("queue");
            var b = a.length;
            for (var c = 0; c < b; c++) {
                var e = a[c];
                if (e.id == d.id) {
                    e.viewer_love = d.viewer_love
                }
            }
            Storage.Set("queue", a)
        }
    }
};
jQuery(window).bind("lalaLoveSong", LoveSong.Love.request);
jQuery(window).bind("lalaUnLoveSong", LoveSong.UnLove.request);
if (typeof (SignIn) == "undefined") {
    SignIn = {}
}
SignIn.LastHistory = "";
SignIn.Showing = false;
SignIn.AllowClose = true;
SignIn.Change = function(a) {
    if (a.href == "sign-in") {
        SignIn.Show()
    } else {
        SignIn.LastHistory = a.href;
        if (SignIn.Showing == true) {
            SignIn.Hide(true)
        }
    }
    if (a.href == "sign-out") {
        location.href = "sign-out"
    }
};
SignIn.Show = function() {
    SignIn.Hide(false);
    jQuery("#full_cover").removeClass("display_none");
    var a = Templates.sign_in();
    jQuery(document.body).append(a);
    jQuery("#sign_in_username").focus();
    if (SignIn.AllowClose == true) {
        jQuery("#sign_in_close_button").bind("click", SignIn.Close)
    }
    jQuery("#sign_in_form").bind("submit", SignIn.Request);
    jQuery("#sign_in_button").bind("click", SignIn.Request);
    jQuery("#sign_in_button").removeClass("inactive");
    jQuery("#sign_in_message").text("");
    jQuery(".sign_in_simple_forgot_password").bind("click", SignIn.PasswordForget.click);
    jQuery(".sign_in_simple_sign_in").bind("click", SignIn.Show);
    jQuery(".create_account_oauth").bind("click", SignIn.Service.click);
    SignIn.Showing = true;
    jQuery(window).trigger({
        type: "lalaModalCreated",
        kind: "signin"
    })
};
SignIn.Hide = function(a) {
    if (a == true) {
        jQuery("#full_cover").addClass("display_none")
    }
    jQuery("#sign_in_button").unbind("click", SignIn.Request);
    jQuery("#sign_in_close_button").unbind("click", SignIn.Close);
    jQuery("#sign_in_form").unbind("submit", SignIn.Request);
    jQuery("#forgot_password_button").unbind("click", SignIn.PasswordForget.submit);
    jQuery("#sign_in_box").remove();
    SignIn.Showing = false
};
SignIn.Close = function() {
    SignIn.Hide(true);
    if (SignIn.LastHistory == "create-account") {
        SignIn.LastHistory = ""
    }
    jQuery(window).trigger({
        type: "lalaNeedHistoryChange",
        href: SignIn.LastHistory
    })
};
SignIn.ModalListener = function(a) {
    if (a.kind != "signin") {
        SignIn.Hide(false)
    }
};
SignIn.Request = function() {
    var b = jQuery("#sign_in_username").attr("value");
    var a = jQuery("#sign_in_password").attr("value");
    jQuery("#sign_in_message").addClass("display_none").text("");
    if (b == "" || a == "") {
        jQuery("#sign_in_message").text("Please enter a username and password")
    } else {
        jQuery("#sign_in_button").addClass("inactive");
        jQuery("#sign_in_button").unbind("click", SignIn.Request);
        jQuery("#sign_in_form").unbind("submit", SignIn.Request);
        jQuery.ajax({
            url: player_root + "modules/login.php",
            type: "POST",
            dataType: "json",
            data: {
                username: b,
                password: a
            },
            complete: SignIn.Response
        })
    }
    return false
};
SignIn.Response = function(b, c) {
    jQuery("#sign_in_button").bind("click", SignIn.Request);
    jQuery("#sign_in_button").removeClass("inactive");
    if (b.status == 200) {
        var a = JSON.parse(b.responseText);
        if (a.status_code == 200) {
            SignIn.SignedIn(a.user)
        } else {
            jQuery("#sign_in_message").removeClass("display_none").text("Please check your password then try again!")
        }
    } else {
        jQuery("#sign_in_message").removeClass("display_none").text("Please check your username and password then try again!")
    }
};
SignIn.SignedIn = function(a) {
    SignIn.Hide(true);
    loggedInUser = a;
    userBackground = a.background;
    jQuery(window).trigger({
        type: "lalaUserLoggedIn",
        user: loggedInUser
    });
    jQuery(window).trigger({
        type: "lalaNeedHistoryChange",
        href: SignIn.LastHistory
    })
    location.reload();
};
SignIn.PasswordForget = {
    method: player_root + "forgot-password.php",
    click: function(a) {
        jQuery("#sign_in_form").addClass("display_none");
        jQuery("#password_reset_form").removeClass("display_none");
        jQuery("#forgot_password_email").focus();
        jQuery("#forgot_password_button").bind("click", SignIn.PasswordForget.submit)
    },
    submit: function() {
        var a = jQuery("#forgot_password_email").attr("value");
        if (a != "") {
            jQuery("#forgot_password_button").unbind("click", SignIn.PasswordForget.submit);
            SignIn.PasswordForget.request(a)
        }
        return false
    },
    request: function(a) {
        jQuery("#sign_in_forgot_message").addClass("display_none");
        jQuery.ajax({
            url: SignIn.PasswordForget.method,
            type: "POST",
            dataType: "json",
            data: {
                email: a
            },
            complete: SignIn.PasswordForget.response
        })
    },
    response: function(a, c) {
        var b = Utils.APIResponse(a, "SignIn.PasswordForget.response");
        if (b.success == true) {
            jQuery(".forgot_password_message").text("An email has been sent with instructions to reset your password.");
            jQuery("#forgot_password_button").remove()
        } else {
            jQuery("#sign_in_forgot_message").removeClass("display_none").text(b.json.status_text)
        }
    }
};
SignIn.Service = {
    click: function() {

        //  jQuery(".create_account_oauth").bind("click", SignIn.Service.click);
        var a = jQuery(this).attr("service");
        window.open("/create-account/" + a);

//        location.reload();
    }
};
jQuery("#sign_out_link").bind("click", function() {
    Storage.Remove("queue");
    Storage.Remove("queueNumber");
    Storage.Remove("LeftSelect.ShowPlaying.section")

});
jQuery(window).bind("lalaModalCreated", SignIn.ModalListener);
jQuery(window).bind("lalaHistoryChange", SignIn.Change);
if (typeof (CreateAccount) == "undefined") {
    CreateAccount = {}
}
CreateAccount.LastHistory = "";
CreateAccount.Showing = false;
CreateAccount.HasProfileInfo = false;
CreateAccount.AccountCreated = false;
CreateAccount.HasService = null;
CreateAccount.Method = player_root + "register";
CreateAccount.Change = function(a) {
    if (a.href == "create-account") {
        if (loggedInUser == null) {
            CreateAccount.Show()
        } else {
            jQuery(window).trigger({
                type: "lalaNeedHistoryChange",
                href: CreateAccount.LastHistory
            })
        }
    } else {
        CreateAccount.LastHistory = a.href;
        if (CreateAccount.Showing == true) {
            CreateAccount.Hide(true)
        }
    }
};
CreateAccount.Show = function() {
    CreateAccount.HasService = null;
    CreateAccount.HasProfileInfo = false;
    CreateAccount.FirelalaUserLoggedInEvent = false;
    CreateAccount.Hide(false);
    jQuery("#full_cover").removeClass("display_none");
    var a = Templates.create_account();
    jQuery(document.body).append(a);
    jQuery("#create_account_username").focus();
    jQuery("#create_account_form").bind("submit", CreateAccount.Request);
    jQuery("#create_account_button").bind("click", CreateAccount.Request);
    jQuery("#create_account_button").removeClass("inactive");
    jQuery("#create_account_message").text("");
    jQuery(".create_account_oauth").bind("click", CreateAccount.Service.click);
    CreateAccount.Showing = true;
    jQuery(window).trigger({
        type: "lalaModalCreated",
        kind: "createAccount"
    })
};
CreateAccount.Hide = function(a) {
    if (a == true) {
        jQuery("#full_cover").addClass("display_none")
    }
    jQuery("#create_account_button").unbind("click", CreateAccount.Request);
    jQuery("#create_account_form").unbind("submit", CreateAccount.Request);
    jQuery(".create_account_oauth").unbind("click", CreateAccount.Service.click);
    jQuery("#create_account_box").remove();
    CreateAccount.Showing = false;
    if (CreateAccount.AccountCreated == true) {
        jQuery(window).trigger({
            type: "lalaUserLoggedIn",
            user: loggedInUser
        })
    }
};
CreateAccount.Close = function() {
    CreateAccount.Hide(true);
    if (CreateAccount.LastHistory == "sign-in") {
        CreateAccount.LastHistory = ""
    }
    jQuery(window).trigger({
        type: "lalaNeedHistoryChange",
        href: CreateAccount.LastHistory
    })
};
CreateAccount.ModalListener = function(a) {
    if (a.kind != "createAccount") {
        CreateAccount.Hide(false)
    }
};
CreateAccount.Request = function() {
    var d = jQuery("#create_account_username").attr("value");
    var b = jQuery("#create_account_email").attr("value");
    var a = jQuery("#create_account_password").attr("value");
    var z = jQuery("#create_check_terms").is(':checked');
    jQuery("#create_account_message").text("").addClass("display_none");
    if (d == "" || a == "") {
        jQuery("#create_account_message").text("Please enter your username, email and password").removeClass("display_none")
    }
    else if (z == false) {

        jQuery("#create_account_message").text("Most Accept Terms and conditions").removeClass("display_none");

    }
    else {
        jQuery("#create_account_button").addClass("inactive");
        jQuery("#create_account_button").unbind("click", CreateAccount.Request);
        var c = CreateAccount.Method;
        if (CreateAccount.HasService != null) {
            c = CreateAccount.Method + "/" + CreateAccount.HasService
        }
        jQuery.ajax({
            url: c,
            type: "POST",
            dataType: "json",
            data: {
                username: d,
                email: b,
                password: a,
                social: CreateAccount.HasService
            },
            complete: CreateAccount.Response
        })
    }
    return false
};
CreateAccount.Response = function(b, c) {
    jQuery("#create_account_button").bind("click", CreateAccount.Request);
    jQuery("#create_account_button").removeClass("inactive");
    if (b.status == 200) {
        var a = JSON.parse(b.responseText);
        if (a.status_code == 200) {
            CreateAccount.AccountCreated = true;
            loggedInUser = a.user;
            userBackground = loggedInUser.background;
            jQuery(window).trigger({
                type: "lalaUserLoggedIn",
                user: loggedInUser
            });
            if (CreateAccount.LastHistory == "sign-in") {
                CreateAccount.LastHistory = ""
            }
            jQuery(window).trigger({
                type: "lalaNeedHistoryChange",
                href: CreateAccount.LastHistory
            })
            location.reload();
            //CreateAccount.Success.welcome()
        } else {
            jQuery("#create_account_message").text(a.status_text).removeClass("display_none")
        }
    }
};
CreateAccount.Success = {
    skip: function() {
        CreateAccount.Hide(true);
    },
    welcome: function() {
        var a = Templates.create_account_welcome();
        jQuery("#create_account_box").html(a);
        jQuery(".create_account_success_next").bind("click", CreateAccount.Success.click)
    },
    click: function() {
        jQuery(".create_account_success_next").unbind("click", CreateAccount.Success.click);
        CreateAccount.LastHistory = jQuery(this).attr("href");
        History.OriginalHref = CreateAccount.LastHistory;
        CreateAccount.Close()
    }
};
CreateAccount.AlreadyLoggedIn = function(a) {
    if (a.success == true) {
        loggedInUser = a.response.user;
        userBackground = a.response.user.background;
        jQuery(window).trigger({
            type: "lalaUserLoggedIn",
            user: loggedInUser
        });
        if (CreateAccount.LastHistory == "sign-in") {
            CreateAccount.LastHistory = ""
        }
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: CreateAccount.LastHistory
        })
    }
};
CreateAccount.Service = {
    click: function() {
        // jQuery(".create_account_oauth").unbind("click", CreateAccount.Service.click);
        var a = jQuery(this).attr("service");
        jQuery(window).bind("lalaServiceConnectionAdd", this, CreateAccount.Service.listener);
        window.open("/create-account/" + a)
    },
    listener: function(b) {

        if (b.success == true) {
            SignIn.Hide();
            if (CreateAccount.Showing == false) {
                CreateAccount.Show()
            }
            CreateAccount.HasService = b.response.service.social;
            CreateAccount.HasProfileInfo = true;
            var a = Templates.create_account_pre_populated({
                service_info: b.response.service.info
            });
            jQuery("#create_account_box").html(a);
            resizeModal();
            jQuery("#create_account_form").bind("submit", CreateAccount.Request);
            jQuery("#create_account_button").bind("click", CreateAccount.Request);
            if (b.response.service.info.email == "") {
                jQuery("#create_account_email").focus()
            } else {
                jQuery("#create_account_password").focus()
            }
        } else {
            jQuery("#create_account_message").text(b.response.message);
            jQuery("#create_account_message").removeClass("display_none")
        }
    }
};
CreateAccount.create = function(b) {

    if (b.success == true) {
        SignIn.Hide();
        if (CreateAccount.Showing == false) {
            CreateAccount.Show()
        }
        CreateAccount.HasService = b.response.service.social;
        CreateAccount.HasProfileInfo = true;
        var a = Templates.create_account_pre_populated({
            service_info: b.response.service.info
        });
        jQuery("#create_account_box").html(a);
        resizeModal();
        jQuery("#create_account_form").bind("submit", CreateAccount.Request);
        jQuery("#create_account_button").bind("click", CreateAccount.Request);
        if (b.response.service.info.email == "") {
            jQuery("#create_account_email").focus()
        } else {
            jQuery("#create_account_password").focus()
        }
    } else {
        jQuery("#create_account_message").text(b.response.message);
        jQuery("#create_account_message").removeClass("display_none")
    }
};
function resizeModal() {
    var b = $("#create_account_box");
    b.css("height", "auto");
    var c = b.height();
    var a = $(window).height();
    if (c < a) {
        b.css("top", (a - c) / 2 - 20);
        b.removeClass("scroll")
    } else {
        b.css({
            height: a - 20,
            top: 10
        }).addClass("scroll")
    }
}
jQuery(window).bind("lalaModalCreated", CreateAccount.ModalListener);
jQuery(window).bind("lalaHistoryChange", CreateAccount.Change);
jQuery(".create_account_later").live("click", CreateAccount.Success.skip);
jQuery("#create_account_close_button").live("click", CreateAccount.Close);

if (typeof (CreatePlaylist) == "undefined") {
    CreatePlaylist = {}
}
CreatePlaylist.Showing = false;
CreatePlaylist.Method = player_root + "more.php?t=playlist&action=create";

CreatePlaylist.Show = function(c) {
    var d;
    jQuery.ajax({
        url: player_root + "more.php?t=highestplaylist",
        type: "POST",
        dataType: "json",
        success: function(b) {

            d = b;
            CreatePlaylist.Id(d);

        }
    })

};
CreatePlaylist.Id = function(a) {
    CreatePlaylist.Hide(false);
    jQuery("#full_cover").removeClass("display_none");
//        console.log(a);
    var b = Templates.create_playlist({
        id: a
    });
//        console.log(b);
    jQuery(document.body).append(b);
    jQuery("#create_playlist_name").focus();
    jQuery("#create_playlist_form").bind("submit", CreatePlaylist.Button.click);
    jQuery("#create_playlist_button").bind("click", CreatePlaylist.Button.click);
    jQuery("#create_playlist_button").removeClass("inactive");
    jQuery("#create_playlist_message").text("");
    CreatePlaylist.Showing = true;
    PlaylistMenu.Hide();
    jQuery(window).trigger({
        type: "lalaModalCreated",
        kind: "ThisCreatePlaylist"
    });
}
CreatePlaylist.Hide = function(a) {
    if (a == true) {
        jQuery("#full_cover").addClass("display_none")
    }
    jQuery("#create_account_button").unbind("click", CreatePlaylist.Request);
    jQuery("#create_playlist_form").unbind("submit", CreatePlaylist.Request);
    jQuery("#create_playlist_box").remove();
    CreatePlaylist.Showing = false;
}
CreatePlaylist.Close = function() {
    CreatePlaylist.Hide(true);
};
CreatePlaylist.ModalListener = function(a) {
    if (a.kind != "ThisCreatePlaylist") {
        CreatePlaylist.Hide(false)
    }
};
CreatePlaylist.Button = {
    count: 0,
    click: function(h) {
        CreatePlaylist.Button.count = 0;
        var f = {};
        var g = false;
        var d = jQuery("#create_playlist_name").attr("value");
        var b = jQuery("#create_playlist_desrc").attr("value");
        //                var e = jQuery("#create_playlist_public").attr("value");
        var e = ($('[name="public"]').is(':checked')) ? 1 : 0;
        jQuery("#create_playlist_message").text("").addClass("display_none");

        if (d == "") {
            jQuery("#create_playlist_message").text("Please enter your playlist name").removeClass("display_none")
        } else {
            CreatePlaylist.Button.start();
            CreatePlaylist.Cover.sending = jQuery("#playlist_cover_uploader_iframe")[0].contentWindow.Uploader.Request();
            if (CreatePlaylist.Cover.sending == true) {
                CreatePlaylist.Button.start();
                CreatePlaylist.Button.count++;
                var c = CreatePlaylist.Method;
                jQuery.ajax({
                    url: c,
                    type: "POST",
                    dataType: "json",
                    data: {
                        name: d,
                        descr: b,
                        access: e
                    },
                    complete: CreatePlaylist.Response
                })
            } else {
                CreatePlaylist.Button.start();
                CreatePlaylist.Button.count++;
                var c = CreatePlaylist.Method;
                jQuery.ajax({
                    url: c,
                    type: "POST",
                    dataType: "json",
                    data: {
                        name: d,
                        descr: b,
                        access: e
                    },
                    complete: CreatePlaylist.Response
                })
            }
        }
    },
    start: function() {
        jQuery("#create_playlist_button").addClass("inactive");
        jQuery("#create_playlist_button").unbind("click", CreatePlaylist.Button.click);
        jQuery("#create_playlist_form").unbind("submit", CreatePlaylist.Button.click)
    },
    done: function(b, a) {
        CreatePlaylist.Button.count--;
        if (CreatePlaylist.Button.count == 0) {
            jQuery("#create_playlist_button").bind("click", CreatePlaylist.Button.click);
            if (b == true) {
                var c = JSON.parse(a.responseText);
                $("#all_playlist_menu").append('<li class="playlist_click" data-playlist-id="' + c.playlist_id + '"><span>' + c.name + '</span></li>');
                jQuery(".playlist_click").bind("click", PlaylistMenu.Click);
                if (SONG_PRE_ADD == "QUEUE") {
                    $.ajax({
                        type: "POST",
                        url: player_root + "more.php?t=playlist&action=addfromqueue&playlist_id=" + c.playlist_id,
                        data: JSON.stringify(AudioPlayer.List.current),
                        contentType: "application/json; charset=utf-8",
                        traditional: true,
                        success: function(response) {
                            return false;
                        }
                    });
                } else {
                    $.ajax({
                        type: "GET",
                        url: player_root + "more.php",
                        data: {
                            song_id: SONG_PRE_ADD,
                            playlist_id: c.playlist_id,
                            t: "playlist",
                            action: "addsong"
                        },
                        success: function(b, c) {
                            return false;
                        }
                    });
                }
                CreatePlaylist.Close()
            } else {
                alert("There was a problem. Please try again")
            }
        }
    }
};
CreatePlaylist.Cover = {
    sending: false,
    done: function(c, a, b) {
        CreatePlaylist.Cover.sending = false;
        CreatePlaylist.Button.done(c, a);
        if (c == true) {
            jQuery(window).trigger({
                type: "lalaPlaylistCoverSet"
            })
        }
    }
};
CreatePlaylist.Request = function() {
    var d = jQuery("#create_playlist_name").attr("value");
    var b = jQuery("#create_playlist_desrc").attr("value");
    jQuery("#create_playlist_message").text("").addClass("display_none");
    if (d == "") {
        jQuery("#create_playlist_message").text("Please enter your playlist name").removeClass("display_none")
    } else {
        jQuery("#create_playlist_button").addClass("inactive");
        jQuery("#create_playlist_button").unbind("click", CreatePlaylist.Request);
        var c = CreatePlaylist.Method;
        jQuery.ajax({
            url: c,
            type: "POST",
            dataType: "json",
            data: {
                name: d,
                descr: b
            },
            complete: CreatePlaylist.Response
        })
    }
    return false
};
CreatePlaylist.Response = function(b, c) {
    var a = JSON.parse(b.responseText);
    jQuery(window).trigger({
        type: "lalaUserAddPlaylist",
        playlist: a.playlist
    });
    CreatePlaylist.Button.done(true, b)
};

jQuery(window).bind("lalaModalCreated", CreatePlaylist.ModalListener);
jQuery("#create_playlist_close_button").live("click", CreatePlaylist.Close);

if (typeof (EditPlaylist) == "undefined") {
    EditPlaylist = {}
}
EditPlaylist.Showing = false;
EditPlaylist.Method = player_root + "more.php?t=playlist&action=edit";

EditPlaylist.Show = function(b) {
    EditPlaylist.Hide(false);
    jQuery("#full_cover").removeClass("display_none");
    var a = Templates.edit_playlist({
        playlist: b
    });

    jQuery(document.body).append(a);
    jQuery("#edit_playlist_name").focus();
    jQuery("#edit_playlist_form").bind("submit", EditPlaylist.Button.click);
    jQuery("#edit_playlist_button").bind("click", EditPlaylist.Button.click);
    jQuery("#edit_playlist_button").removeClass("inactive");
    jQuery("#edit_playlist_message").text("");
    EditPlaylist.Showing = true;
    jQuery(window).trigger({
        type: "lalaModalCreated",
        kind: "ThisEditPlaylist"
    })
};
EditPlaylist.Hide = function(a) {
    if (a == true) {
        jQuery("#full_cover").addClass("display_none")
    }
    jQuery("#edit_account_button").unbind("click", EditPlaylist.Request);
    jQuery("#edit_playlist_form").unbind("submit", EditPlaylist.Request);
    jQuery("#edit_playlist_box").remove();
    EditPlaylist.Showing = false;
};
EditPlaylist.Close = function() {
    EditPlaylist.Hide(true);
};
EditPlaylist.ModalListener = function(a) {
    if (a.kind != "ThisEditPlaylist") {
        EditPlaylist.Hide(true)
    }
};
EditPlaylist.Button = {
    count: 0,
    click: function(h) {
        EditPlaylist.Button.count = 0;
        var f = {};
        var g = false;
        var d = jQuery("#edit_playlist_name").attr("value");
        var b = jQuery("#edit_playlist_desrc").attr("value");
        var h = jQuery("#edit_playlist_id").attr("value");
        var a = jQuery("#edit_playlist_public").prop("checked");
        var checked = ($('[name="edit_public"]').is(':checked')) ? 1 : 0;
        //                alert(checked);

        jQuery("#edit_playlist_message").text("").addClass("display_none");
        if (d == "") {
            jQuery("#edit_playlist_message").text("Please enter your playlist name").removeClass("display_none")
        } else {

            EditPlaylist.Button.start();
            EditPlaylist.Avatar.sending = jQuery("#playlist_cover_uploader_iframe")[0].contentWindow.Uploader.Request();

            if (EditPlaylist.Avatar.sending == true) {
                EditPlaylist.Button.start();
                EditPlaylist.Button.count++;
                var c = EditPlaylist.Method;

                jQuery.ajax({
                    url: c,
                    type: "POST",
                    dataType: "json",
                    data: {
                        name: d,
                        descr: b,
                        approve: checked,
                        id: h

                    },
                    complete: EditPlaylist.Response
                })
            } else {
                EditPlaylist.Button.start();
                EditPlaylist.Button.count++;
                var c = EditPlaylist.Method;

                jQuery.ajax({
                    url: c,
                    type: "POST",
                    dataType: "json",
                    data: {
                        name: d,
                        descr: b,
                        approve: checked,
                        id: h
                    },
                    complete: EditPlaylist.Response
                })
            }
        }
    },
    start: function() {
        jQuery("#edit_playlist_button").addClass("inactive");
        jQuery("#edit_playlist_button").unbind("click", EditPlaylist.Button.click);
        jQuery("#edit_playlist_form").unbind("submit", EditPlaylist.Button.click)
    },
    done: function(b, a) {
        EditPlaylist.Button.count--;
        if (EditPlaylist.Button.count == 0) {
            jQuery("#edit_playlist_button").bind("click", EditPlaylist.Button.click);
            if (b == true) {
                EditPlaylist.Close()
            } else {
                alert("There was a problem. Please try again")
            }
        }
    }
};
EditPlaylist.Avatar = {
    sending: false,
    done: function(c, a, b) {
        jQuery("#playlist_top_cover_img").attr("src", player_root + 'static/playlists/' + a.image);
        EditPlaylist.Avatar.sending = false;
        EditPlaylist.Button.done(c, a);
    }
};
EditPlaylist.Request = function() {
    var d = jQuery("#edit_playlist_name").attr("value");
    var b = jQuery("#edit_playlist_desrc").attr("value");
    jQuery("#edit_playlist_message").text("").addClass("display_none");
    if (d == "") {
        jQuery("#edit_playlist_message").text("Please enter your playlist name").removeClass("display_none")
    } else {
        jQuery("#edit_playlist_button").addClass("inactive");
        jQuery("#edit_playlist_button").unbind("click", EditPlaylist.Request);
        var c = EditPlaylist.Method;
        jQuery.ajax({
            url: c,
            type: "POST",
            dataType: "json",
            data: {
                name: d,
                descr: b
            },
            complete: EditPlaylist.Response
        })
    }
    return false
};
EditPlaylist.Response = function(b, c) {
    var a = JSON.parse(b.responseText);
    jQuery("#playlist_name").html(a.name);
    jQuery("#playlist_descr").html(a.descr);
    EditPlaylist.Button.done(true, b)
};

/*jQuery(window).bind("lalaModalCreated", EditPlaylist.ModalListener);*/
jQuery("#edit_playlist_close_button").live("click", EditPlaylist.Close);

if (typeof (TopRightDropdown) == "undefined") {
    TopRightDropdown = {}
}
TopRightDropdown.Open = false;
TopRightDropdown.Init = function(a) {
    if (a.user != null) {
        jQuery("#top_right").bind("click", TopRightDropdown.Click);
        jQuery(".top_right_dropdown_link").bind("click", TopRightDropdown.Close);
    }
};
TopRightDropdown.Click = function(a) {
    a.stopPropagation();
    jQuery(document).bind("click", TopRightDropdown.DocumentClick);
    if (TopRightDropdown.Open == false) {
        TopRightDropdown.Show()
    } else {
        TopRightDropdown.Close()
    }
};
TopRightDropdown.DocumentClick = function(a) {
    if (TopRightDropdown.Open == true) {
        TopRightDropdown.Close()
    }
};
TopRightDropdown.Show = function() {
    jQuery("#top_right").addClass("selected");
    jQuery("#top_right_dropdown").removeClass("display_none");
    TopRightDropdown.Open = true
};
TopRightDropdown.Close = function() {
    jQuery("#top_right").removeClass("selected");
    jQuery("#top_right_dropdown").addClass("display_none");
    jQuery(document).unbind("click", TopRightDropdown.DocumentClick);
    TopRightDropdown.Open = false
};
TopRightDropdown.RefreshAvatar = function() {
    if (loggedInUser.image.small) {
        jQuery("#logged_in_user_avatar").attr("src", loggedInUser.image.small + "?" + Math.random())
    } else {
        jQuery("#logged_in_user_avatar").attr("src", "/static/users/avatar_small_default.png" + "?" + Math.random())
    }
};
jQuery(window).bind("lalaAvatarSet", TopRightDropdown.RefreshAvatar);
jQuery(window).bind("lalaUIInit", TopRightDropdown.Init);

if (typeof (QueueToPlaylist) == "undefined") {
    QueueToPlaylist = {}
}
QueueToPlaylist.Open = false;
QueueToPlaylist.Init = function(a) {
    if (a.user != null) {
        jQuery("#current_playlist_save").bind("click", QueueToPlaylist.Click);
        jQuery(".queue_to_playlist_dropdown_link").bind("click", function() {
            QueueToPlaylist.Close
            var PLAYLIST_ID = $(this).attr("data-playlist-id");
            $.ajax({
                type: "POST",
                url: player_root + "more.php?t=playlist&action=addfromqueue&playlist_id=" + PLAYLIST_ID,
                data: JSON.stringify(AudioPlayer.List.current),
                contentType: "application/json; charset=utf-8",
                traditional: true,
                success: function(response) {
                    return false;
                }
            });
        });
    }
};
QueueToPlaylist.Click = function(a) {
    a.stopPropagation();
    jQuery(document).bind("click", QueueToPlaylist.DocumentClick);
    if (QueueToPlaylist.Open == false) {
        QueueToPlaylist.Show()
    } else {
        QueueToPlaylist.Close()
    }
};
QueueToPlaylist.DocumentClick = function(a) {
    if (QueueToPlaylist.Open == true) {
        QueueToPlaylist.Close()
    }
};
QueueToPlaylist.Show = function() {
    jQuery("#queue_to_playlist_dropdown").removeClass("display_none");
    QueueToPlaylist.Open = true
};
QueueToPlaylist.Close = function() {
    jQuery("#queue_to_playlist_dropdown").addClass("display_none");
    jQuery(document).unbind("click", QueueToPlaylist.DocumentClick);
    QueueToPlaylist.Open = false
};
jQuery(window).bind("lalaUIInit", QueueToPlaylist.Init);

if (typeof (PlaylistMenu) == "undefined") {
    PlaylistMenu = {}
}
PlaylistMenu.Open = false;
PlaylistMenu.Init = function(a) {
    //if (a.user != null) {
    jQuery(".playlist_click").bind("click", PlaylistMenu.Click);

    jQuery("#create_playlist_click").bind("click", function() {

        if (a.user != null) {
            CreatePlaylist.Show()
        } else {
            //jQuery(b).css("opacity", 1);
            alert("You must login to use this feature!", true)
        }

    });
    jQuery(".queue_to_new_playlist_dropdown_link").bind("click", function() {
        SONG_PRE_ADD = "QUEUE";
        CreatePlaylist.Show()
    });
//}
};
PlaylistMenu.Click = function(a) {
    a.stopPropagation();
    var PLAYLIST_ID = $(this).attr("data-playlist-id");
    $('#dropdown-1').hide();
    $.ajax({
        type: "GET",
        url: player_root + "more.php",
        data: {
            song_id: SONG_PRE_ADD,
            playlist_id: PLAYLIST_ID,
            t: "playlist",
            action: "addsong"
        },
        success: function(response) {
            return false;
        }
    });
};
PlaylistMenu.Show = function(a) {
    if (loggedInUser != null) {
        $('#dropdown-1').show();
        PlaylistMenu.Position(a);
        $("#right").bind("scroll", PlaylistMenu.Hide);
    }
};

PlaylistMenu.Hide = function() {
    $('#dropdown-1').hide();
};
PlaylistMenu.Position = function(a) {
    var dropdown = $('.dropdown:visible').eq(0),
            trigger = a,
            hOffset = trigger ? parseInt(trigger.attr('data-horizontal-offset') || 0) : null,
            vOffset = trigger ? parseInt(trigger.attr('data-vertical-offset') || 0) : null;
    if (dropdown.length === 0 || !trigger)
        return;
    dropdown
            .css({
                left: dropdown.hasClass('dropdown-anchor-right') ?
                        trigger.offset().left - (dropdown.outerWidth() - trigger.outerWidth()) + hOffset : trigger.offset().left + hOffset,
                top: trigger.offset().top + trigger.outerHeight() + vOffset
            });
};


jQuery(window).bind("lalaUIInit", PlaylistMenu.Init);

if (typeof (BrowseHistory) == "undefined") {
    BrowseHistory = {}
}

BrowseHistory.SongsList = [];
BrowseHistory.Built = false;
BrowseHistory.CurrentSongs = [];
BrowseHistory.LastHistory = "";
BrowseHistory.Showing = false;
BrowseHistory.Build = {
    init: function() {
        if (BrowseHistory.Built == false) {
            BrowseHistory.SongsList = [];
            var g = Storage.Get("sitesSongs");
            if (g != null) {
                var e = {};
                var a = {};
                var c = {};
                for (var d in g) {
                    var f = g[d];
                    if (f.id != null) {
                        BrowseHistory.SongsList.push(f);
                        var b = Utils.CleanHref(f.source);
                        e[b] = 1;
                        a[f.artist] = 1;
                        c[f.album] = 1
                    }
                }

                BrowseHistory.Artists.build(a);
                BrowseHistory.Albums.build(c);
                BrowseHistory.Songs.build(BrowseHistory.SongsList, false);
                BrowseHistory.Built = true
            }
        }
    },
    getSorted: function(d) {
        var b = [];
        for (var c in d) {
            if (c != "null" && c != "") {
                b.push(c)
            }
        }
        b.sort();
        return b
    }
};
BrowseHistory.Sites = {
    selected: null,
    selectedElement: null,
    click: function(a) {
        BrowseHistory.Sites.unSelect(BrowseHistory.Sites.selectedElement);
        BrowseHistory.Sites.unHalfSelect(BrowseHistory.Sites.selectedElement);
        BrowseHistory.Sites.selected = jQuery(this).text();
        BrowseHistory.Sites.selectedElement = this;
        BrowseHistory.Sites.select(BrowseHistory.Sites.selectedElement);
        BrowseHistory.Sites.choose(BrowseHistory.Sites.selected)
    },
    select: function(a) {
        jQuery(a).addClass("selected")
    },
    unSelect: function(a) {
        jQuery(a).removeClass("selected")
    },
    halfSelect: function() {
        jQuery(BrowseHistory.Sites.selectedElement).addClass("half_selected")
    },
    unHalfSelect: function(a) {
        jQuery(a).removeClass("half_selected")
    },
    choose: function(f) {
        var a = BrowseHistory.SongsList.length;
        var b = {};
        var d = {};
        var g = [];
        for (var e = 0; e < a; e++) {
            var h = BrowseHistory.SongsList[e];
            var c = Utils.CleanHref(h.source);
            if (c == f) {
                b[h.artist] = 1;
                d[h.album] = 1;
                g.push(h)
            }
        }
        BrowseHistory.Artists.build(b);
        BrowseHistory.Albums.build(d);
        BrowseHistory.Songs.build(g, true)
    },
    build: function(g) {
        var c = BrowseHistory.Build.getSorted(g);
        var b = c.length;
        var d = Templates.sites_top;
        var f = "";
        for (var e = 0; e < b; e++) {
            f += d({
                type: "site",
                value: c[e]
            })
        }
        jQuery("#song_history_site_bottom").html(f);
        BrowseHistory.Sites.selected = null
    }
};
BrowseHistory.Artists = {
    selected: null,
    selectedElement: null,
    click: function(a) {
        BrowseHistory.Artists.unSelect(BrowseHistory.Artists.selectedElement);
        BrowseHistory.Artists.unHalfSelect(BrowseHistory.Artists.selectedElement);
        BrowseHistory.Artists.selected = jQuery(this).text();
        BrowseHistory.Artists.selectedElement = this;
        BrowseHistory.Artists.select(BrowseHistory.Artists.selectedElement);
        BrowseHistory.Sites.halfSelect();
        BrowseHistory.Artists.choose(BrowseHistory.Artists.selected)
    },
    select: function(a) {
        jQuery(a).addClass("selected")
    },
    unSelect: function(a) {
        jQuery(a).removeClass("selected")
    },
    halfSelect: function() {
        jQuery(BrowseHistory.Artists.selectedElement).addClass("half_selected")
    },
    unHalfSelect: function(a) {
        jQuery(a).removeClass("half_selected")
    },
    choose: function(b) {
        var a = BrowseHistory.SongsList.length;
        var d = {};
        var f = [];
        if (BrowseHistory.Sites.selected != null) {
            for (var e = 0; e < a; e++) {
                var g = BrowseHistory.SongsList[e];
                var c = Utils.CleanHref(g.source);
                if (c == BrowseHistory.Sites.selected && g.artist == b) {
                    d[g.album] = 1;
                    f.push(g)
                }
            }
        } else {
            for (var e = 0; e < a; e++) {
                var g = BrowseHistory.SongsList[e];
                if (g.artist == b) {
                    d[g.album] = 1;
                    f.push(g)
                }
            }
        }
        BrowseHistory.Albums.build(d);
        BrowseHistory.Songs.build(f, true)
    },
    build: function(g) {
        var c = BrowseHistory.Build.getSorted(g);
        var b = c.length;
        var d = Templates.sites_top;
        var f = "";
        for (var e = 0; e < b; e++) {
            f += d({
                type: "artist",
                value: c[e]
            })
        }
        jQuery("#song_history_artist_bottom").html(f);
        BrowseHistory.Artists.selected = null
    }
};
BrowseHistory.Albums = {
    selected: null,
    selectedElement: null,
    click: function(a) {
        BrowseHistory.Albums.unSelect(BrowseHistory.Albums.selectedElement);
        BrowseHistory.Albums.unHalfSelect(BrowseHistory.Albums.selectedElement);
        BrowseHistory.Albums.selected = jQuery(this).text();
        BrowseHistory.Albums.selectedElement = this;
        BrowseHistory.Albums.select(BrowseHistory.Albums.selectedElement);
        BrowseHistory.Sites.halfSelect();
        BrowseHistory.Artists.halfSelect();
        BrowseHistory.Albums.choose(BrowseHistory.Albums.selected)
    },
    select: function(a) {
        jQuery(a).addClass("selected")
    },
    unSelect: function(a) {
        jQuery(a).removeClass("selected")
    },
    halfSelect: function() {
        jQuery(BrowseHistory.Albums.selectedElement).addClass("half_selected")
    },
    unHalfSelect: function(a) {
        jQuery(a).removeClass("half_selected")
    },
    choose: function(g) {
        var a = BrowseHistory.SongsList.length;
        var c = {};
        var e = [];
        if (BrowseHistory.Sites.selected != null && BrowseHistory.Artists.selected != null) {
            for (var d = 0; d < a; d++) {
                var f = BrowseHistory.SongsList[d];
                var b = Utils.CleanHref(f.source);
                if (b == BrowseHistory.Sites.selected && f.artist == BrowseHistory.Artists.selected && g == f.album) {
                    c[f.album] = 1;
                    e.push(f)
                }
            }
        }
        if (BrowseHistory.Sites.selected != null && BrowseHistory.Artists.selected == null) {
            for (var d = 0; d < a; d++) {
                var f = BrowseHistory.SongsList[d];
                var b = Utils.CleanHref(f.source);
                if (b == BrowseHistory.Sites.selected && g == f.album) {
                    c[f.album] = 1;
                    e.push(f)
                }
            }
        }
        if (BrowseHistory.Sites.selected == null && BrowseHistory.Artists.selected != null) {
            for (var d = 0; d < a; d++) {
                var f = BrowseHistory.SongsList[d];
                var b = Utils.CleanHref(f.source);
                if (f.artist == BrowseHistory.Artists.selected && g == f.album) {
                    c[f.album] = 1;
                    e.push(f)
                }
            }
        }
        if (BrowseHistory.Sites.selected == null && BrowseHistory.Artists.selected == null) {
            for (var d = 0; d < a; d++) {
                var f = BrowseHistory.SongsList[d];
                var b = Utils.CleanHref(f.source);
                if (g == f.album) {
                    c[f.album] = 1;
                    e.push(f)
                }
            }
        }
        BrowseHistory.Songs.build(e, true)
    },
    build: function(g) {
        var c = BrowseHistory.Build.getSorted(g);
        var b = c.length;
        var d = Templates.sites_top;
        var f = "";
        for (var e = 0; e < b; e++) {
            f += d({
                type: "album",
                value: c[e]
            })
        }
        jQuery("#song_history_album_bottom").html(f);
        BrowseHistory.Albums.selected = null
    }
};
BrowseHistory.Songs = {
    playing: null,
    dblClick: function(b) {
        var a = parseInt(jQuery(this).attr("position"));
        jQuery(window).trigger({
            type: "lalaNewSongList",
            list: BrowseHistory.CurrentSongs,
            position: a,
            section: "browse"
        });
        b.preventDefault();
        window.getSelection().removeAllRanges();
        return false
    },
    playButtonClick: function(b) {
        var a = parseInt(jQuery(this).parent().attr("position"));
        jQuery(window).trigger({
            type: "lalaNewSongList",
            list: BrowseHistory.CurrentSongs,
            position: a,
            section: "browse"
        })
    },
    build: function(h, b) {
        BrowseHistory.CurrentSongs = [];
        var a = h.length;
        var f = true;
        var d = "<div>";
        h.sort(BrowseHistory.Songs.sortByDate);
        var e = Templates.sites_songs;
        for (var c = 0; c < a; c++) {
            var g = h[c];
            if (g.id != null) {
                d += e({
                    song: g,
                    position: c
                });
                BrowseHistory.CurrentSongs.push(g)
            }
        }
        d += "</div>";
        jQuery("#song_history_song_bottom").html(d)
    },
    sortByDate: function(d, c) {
        return c.timestamp - d.timestamp
    },
    change: function(a) {
        jQuery(BrowseHistory.Songs.playing).removeClass("playing");
        var b = a.song;
        BrowseHistory.Songs.playing = jQuery(".song_play_" + b.domainkey);
        jQuery(BrowseHistory.Songs.playing).addClass("playing")
    },
    loved: {
        click: function(b) {
            var a = parseInt(jQuery(this).parent().attr("position"));
            var c = BrowseHistory.CurrentSongs[a];
            if (c.id) {
                $(this).addClass("loading");
                $(window).trigger({
                    type: ($(this).hasClass("on")) ? "lalaUnLoveSong" : "lalaLoveSong",
                    song: c
                })
            } else {
                alert("There was a problem. Please try again.")
            }
        }
    }
};
BrowseHistory.Change = function(a) {
    if (a.href == "browse") {
        alert("This features in developing !");
        jQuery("#right").addClass("history");
        jQuery("#history_toggle").addClass("selected");
        BrowseHistory.Showing = true;
        Utils.HideSections();
        jQuery("#song_history").removeClass("display_none");
        BrowseHistory.Build.init()
    } else {
        jQuery("#right").removeClass("history");
        jQuery("#history_toggle").removeClass("selected");
        BrowseHistory.Showing = false;
        BrowseHistory.LastHistory = a.href
    }
};
BrowseHistory.NeedChange = {
    click: function() {
        if (BrowseHistory.Showing == false) {
            BrowseHistory.NeedChange.change()
        } else {
            jQuery(window).trigger({
                type: "lalaNeedHistoryChange",
                href: BrowseHistory.LastHistory
            })
        }
    },
    inputClick: function(a) {
        a.stopPropagation()
    },
    focus: function() {
        if (BrowseHistory.Showing == false) {
            BrowseHistory.NeedChange.change()
        }
    },
    change: function() {
        jQuery("#search_box").addClass("selected");
        setTimeout(function() {
            jQuery(window).trigger({
                type: "lalaNeedHistoryChange",
                href: "history"
            })
        }, 100)
    }
};
BrowseHistory.Clear = function(a) {
    jQuery("#search_box_input").attr("value", "");
    BrowseHistory.Search.go("");
    jQuery("#search_box_input").focus();
    a.stopPropagation()
};
BrowseHistory.Search = {
    keyup: function(b) {
        var a = jQuery(this).attr("value");
        BrowseHistory.Search.go(a)
    },
    go: function(q) {
        if (BrowseHistory.Showing == false) {
            BrowseHistory.NeedChange.change()
        }
        if (q.length > 0) {
            jQuery("#search_box_clear").removeClass("hidden");
            var p = Storage.Get("sitesSongs");
            if (p != null) {
                var c = [];
                for (var g in p) {
                    var f = p[g];
                    if (f.id != null) {
                        c.push(f)
                    }
                }
                var k = c.length;
                var m = {};
                var n = {};
                var d = {};
                var o = {};
                var b = [];
                for (var g = 0; g < k; g++) {
                    var f = c[g];
                    var a = Utils.CleanHref(f.source);
                    if (Utils.HasValue(f.title) == true) {
                        var s = f.title.toLowerCase().search(q.toLowerCase())
                    }
                    if (Utils.HasValue(a) == true) {
                        var r = a.toLowerCase().search(q.toLowerCase())
                    }
                    var h = -1;
                    var l = -1;
                    if (Utils.HasValue(f.artist) == true) {
                        h = f.artist.toLowerCase().search(q.toLowerCase())
                    }
                    if (Utils.HasValue(f.album) == true) {
                        l = f.album.toLowerCase().search(q.toLowerCase())
                    }
                    if (s != -1 || r != -1 || h != -1 || l != -1) {
                        o[f.domainkey] = f;
                        m[a] = 1;
                        n[f.artist] = 1;
                        d[f.album] = 1
                    }
                }
                BrowseHistory.SongsList = [];
                for (var e in o) {
                    BrowseHistory.SongsList.push(o[e])
                }
                BrowseHistory.Sites.build(m);
                BrowseHistory.Artists.build(n);
                BrowseHistory.Albums.build(d);
                BrowseHistory.Songs.build(BrowseHistory.SongsList, false)
            }
        } else {
            BrowseHistory.Built = false;
            BrowseHistory.Build.init();
            jQuery("#search_box_clear").addClass("hidden")
        }
    }
};
BrowseHistory.StorageChanged = function(a) {
    if (a.originalEvent.key == "sitesSongs") {
        BrowseHistory.Built = false
    }
};
jQuery(window).bind("lalaHistoryChange", BrowseHistory.Change);
jQuery(".song_history_pane_bottom_row_site").live("click", BrowseHistory.Sites.click);
jQuery(".song_history_pane_bottom_row_artist").live("click", BrowseHistory.Artists.click);
jQuery(".song_history_pane_bottom_row_album").live("click", BrowseHistory.Albums.click);
jQuery(".song_history_pane_bottom_row_song").live("dblclick", BrowseHistory.Songs.dblClick);
jQuery(window).bind("lalaAudioNewSong", BrowseHistory.Songs.change);
jQuery(".song_history_play_button").live("click", BrowseHistory.Songs.playButtonClick);
jQuery("#search_box_input").bind("keyup", BrowseHistory.Search.keyup);
jQuery("#search_box_input").bind("focus", BrowseHistory.NeedChange.focus);
jQuery("#search_box_input").bind("click", BrowseHistory.NeedChange.inputClick);
jQuery("#history_toggle").bind("click", BrowseHistory.NeedChange.click);
jQuery("#search_box_clear").bind("click", BrowseHistory.Clear);
jQuery(window).bind("storage", BrowseHistory.StorageChanged);
jQuery(document).bind("storage", BrowseHistory.StorageChanged);
jQuery(".song_history_loved_bottom").live("click", BrowseHistory.Songs.loved.click);
if (typeof (DocTitle) == "undefined") {
    DocTitle = {}
}
DocTitle.CurrentSong = null;
DocTitle.SongChange = function(a) {
    DocTitle.CurrentSong = a.song;
    DocTitle.Set(false)
};
DocTitle.CurrentSong = function(a) {
    DocTitle.CurrentSong = a.song;
    DocTitle.Set(a.paused)
};
DocTitle.Play = function(a) {
    DocTitle.Set(false)
};
DocTitle.Pause = function(a) {
    DocTitle.Set(true)
};
DocTitle.Set = function(a) {
    var c = "";
    if (a == false) {
        c = "▶"
    }
    try {
        if (DocTitle.CurrentSong.title != null) {
            if (a == false) {
                c = "▶ " + DocTitle.CurrentSong.title
            } else {
                c = DocTitle.CurrentSong.title
            }
        }
        if (Utils.HasValue(DocTitle.CurrentSong.artist)) {
            c += " by " + DocTitle.CurrentSong.artist
        }
    } catch (b) {
    }
    c += " - " + WEB_TITLE;
    document.title = Utils.ReplaceHTMLEncoding(c)
};
DocTitle.Reset = function() {
    document.title = WEB_TITLE
};
jQuery(window).bind("lalaAudioNewSong", DocTitle.SongChange);
jQuery(window).bind("lalaAudioPlay", DocTitle.Play);
jQuery(window).bind("lalaAudioPause", DocTitle.Pause);
jQuery(window).bind("lalaAudioStop", DocTitle.Reset);
jQuery(window).bind("lalaAudioCurrentSong", DocTitle.CurrentSong);
var FeedView = SongListView.extend({
    collectionClass: SongLovedFeedCollection,
    section: "feed",
    render: function() {
        $(this.el).html(Templates.feed({
            title: "Your Feed",
            item_rows_classname: "feed"
        }));
        return this
    }
});
var TastemakersView = SongListView.extend({
    collectionClass: SongLovedFeedCollection,
    section: "tastemakers",
    render: function() {
        $(this.el).html(Templates.feed({
            title: "Tastemakers",
            item_rows_classname: "feed"
        }));
        return this
    }
});
if (typeof (ShareBox) == "undefined") {
    ShareBox = {}
}
ShareBox.Listen = function(a) {
    ShareBox.Show(a.song)
};
ShareBox.InApp = true;
ShareBox.Song = null;
ShareBox.Value = "";
ShareBox.IsShowing = false;
ShareBox.Show = function(b) {
    if (ShareBox.IsShowing == false) {
        ShareBox.Song = b;
        jQuery("#full_cover").removeClass("display_none");
        var a = Templates.share_box({
            shared: false,
            song: ShareBox.Song
        });
        jQuery(document.body).append(a);
        ShareBox.AddListeners();
        ShareBox.IsShowing = true
    }
};
ShareBox.Hide = function() {
    jQuery("#share_box_close_button").unbind("click", ShareBox.Hide);
    jQuery("#share_box_textarea").unbind("keyup", ShareBox.TextArea.keyup);
    jQuery("#share_box_icon_twitter").unbind("click", ShareBox.Twitter.click);
    jQuery("#share_box_icon_facebook").unbind("click", ShareBox.Facebook.click);
    jQuery("#share_button").unbind("click", ShareBox.Share.click);
    jQuery("#share_box").remove();
    jQuery("#full_cover").addClass("display_none");
    ShareBox.Service.selected = null;
    ShareBox.Song = null;
    jQuery(window).trigger({
        type: "lalaShareBoxClose"
    });
    ShareBox.IsShowing = false
};
ShareBox.AddListeners = function() {
    jQuery("#share_box_close_button").bind("click", ShareBox.Hide);
    jQuery("#share_box_icon_twitter").bind("click", ShareBox.Twitter.click);
    jQuery("#share_box_icon_facebook").bind("click", ShareBox.Facebook.click);
    jQuery("#share_button").bind("click", ShareBox.Share.click);
    jQuery("#share_button").removeClass("inactive")
};
ShareBox.TextArea = {
    keyup: function(b) {
        var a = jQuery("#share_box_textarea").attr("value");
        jQuery("#share_box_count").text(140 - a.length)
    }
};
ShareBox.Twitter = {
    message: null,
    tweet: function() {
        var a = "";
        if (Utils.HasValue(ShareBox.Song.artist)) {
            a = " by " + ShareBox.Song.artist
        }
        return "Listening to " + ShareBox.Song.title + a + " on #Nota - http://" + location.host + "/song/" + ShareBox.Song.id
    },
    click: function(b) {
        if (ShareBox.Service.selected != "twitter") {
            ShareBox.Service.removeAll();
            ShareBox.Service.select("twitter");
            ShareBox.Twitter.message = ShareBox.Twitter.tweet();
            var a = jQuery("#share_box_textarea").attr("value");
            if (a != "") {
                ShareBox.Twitter.message = jQuery.trim(a) + " " + ShareBox.Twitter.message
            }
            jQuery("#share_box_textarea").attr("value", ShareBox.Twitter.message);
            jQuery("#share_box_count").text(140 - ShareBox.Twitter.message.length);
            jQuery("#share_box_textarea").bind("keyup", ShareBox.TextArea.keyup)
        }
    }
};
ShareBox.Facebook = {
    message: null,
    click: function(d) {
        if (ShareBox.Service.selected != "facebook") {
            ShareBox.Service.removeAll();
            ShareBox.Service.select("facebook");
            ShareBox.Facebook.message = "";
            var b = jQuery("#share_box_textarea").attr("value");
            var c = b.split(ShareBox.Twitter.tweet());
            for (var a in c) {
                if (c[a] != "") {
                    ShareBox.Facebook.message = jQuery.trim(ShareBox.Facebook.message + " " + jQuery.trim(c[a]))
                }
            }
            jQuery("#share_box_textarea").attr("value", ShareBox.Facebook.message);
            jQuery("#share_box_count").text("");
            jQuery("#share_box_textarea").unbind("keyup", ShareBox.TextArea.keyup)
        }
    }
};
ShareBox.Service = {
    selected: null,
    select: function(a) {
        ShareBox.Service.selected = a;
        jQuery("#share_box_icon_" + a).addClass("selected")
    },
    removeAll: function() {
        jQuery("#share_box_icon_twitter").removeClass("selected");
        jQuery("#share_box_icon_facebook").removeClass("selected")
    }
};
ShareBox.Share = {
    method: player_root + "api/share.php?service=%service%&songid=%songid%",
    click: function(a) {
        ShareBox.Value = jQuery("#share_box_textarea").attr("value");
        if (ShareBox.Service.selected != null) {
            jQuery("#share_button").unbind("click", ShareBox.Share.click);
            jQuery("#share_button").addClass("inactive");
            jQuery("#share_box_icon_twitter").unbind("click", ShareBox.Twitter.click);
            jQuery("#share_box_icon_facebook").unbind("click", ShareBox.Facebook.click);
            jQuery("#share_box_middle_error").addClass("display_none");
            ShareBox.Share.request(ShareBox.Value, ShareBox.Song, ShareBox.Service.selected)
        }
    },
    request: function(b, c, a) {
        var d = ShareBox.Share.method.replace("%service%", a);
        d = d.replace("%songid%", c.id);
        b = encodeURIComponent(b);
        b = b.replace("%E2%99%AA", "?");
        if (a == "facebook") {
            window.open("https://www.facebook.com/sharer/sharer.php?u=" + player_root + "song/" + c.id + "&t=" + b, "_blank")
        } else {
            if (a == "twitter") {
                window.open("https://twitter.com/intent/tweet?text=" + b, "_blank")
            }
        }
        ShareBox.Hide()
    }
};
ShareBox.Connect = {
    render: function() {
        if (ShareBox.InApp == true) {
            var a = Templates.add_service_modal({
                service: ShareBox.Service.selected
            });
            jQuery("#share_box_middle").html(a);
            jQuery("#share_box_bottom").empty();
            jQuery(".create_account_oauth").bind("click", ShareBox.Connect.click)
        } else {
            jQuery("#share_box_bottom").empty();
            jQuery("#share_box_middle").html('<div id="add_service_modal_middle"><div id="add_service_text">Thanks for sharing this song! <br />First you must connect with ' + Utils.Capitalize(ShareBox.Service.selected) + '.</div><a id="add_service_lala_link" class="modal_bottom_button generic_button" href="http://nota.kg" target="_blank">Go to Nota</a><div class="clear"></div>')
        }
    },
    click: function() {
        jQuery(".create_account_oauth").unbind("click", ShareBox.Connect.click);
        jQuery(window).bind("lalaServiceConnectionAdd", ShareBox.Connect.listener);
        var a = jQuery(this).attr("service");
        window.open("/connect/" + a);
        Utils.ShowLoading("#add_service_loading")
    },
    listener: function(b) {
        jQuery(window).unbind("lalaServiceConnectionAdd", ShareBox.Connect.listener);
        var a = b.obj;
        if (a.type) {
            ShareBox.Share.request(ShareBox.Value, ShareBox.Song, ShareBox.Service.selected)
        }
    }
};
jQuery(window).bind("lalaShareSong", ShareBox.Listen);
if (typeof (AlertBox) == "undefined") {
    AlertBox = {}
}
AlertBox.Close = function() {
    jQuery("#alert_box").remove();
    jQuery(document).unbind("keydown", AlertBox.Keydown)
};
AlertBox.Keydown = function(a) {
    if (a.keyCode == 13) {
        AlertBox.Close()
    }
    a.stopPropagation()
};
AlertBox.ModalListener = function(a) {
    if (a.kind != "alert") {
        AlertBox.Close()
    }
};
window.alert = function(c, a) {
    AlertBox.Close();
    var b = Templates.alert({
        alert_str: c,
        show_login: a
    });
    jQuery(document.body).append(b);
    jQuery("#alert_box_close_button").bind("click", AlertBox.Close);
    jQuery("#alert_button").bind("click", AlertBox.Close);
    jQuery(document).bind("keydown", AlertBox.Keydown);
    jQuery("#alert_button").attr("tabindex", -1).focus();
    jQuery(window).trigger({
        type: "lalaModalCreated",
        kind: "alert"
    })
};
jQuery(window).bind("lalaModalCreated", AlertBox.ModalListener);
if (typeof (SettingsProfile) == "undefined") {
    SettingsProfile = {}
}
SettingsProfile.Build = function() {
    jQuery(".settings_tab").removeClass("selected");
    jQuery("#settings_profile").addClass("selected");
    jQuery("#settings_middle").html("");
    SettingsProfile.Get.request()
};
SettingsProfile.Get = {
    method: player_root + "more.php?t=profile&username=",
    request: function() {
        if (loggedInUser != null) {
            Utils.ShowLoading("#settings_middle");
            var a = Utils.get_cookie("_xsrf");
            jQuery.ajax({
                url: SettingsProfile.Get.method + loggedInUser.username,
                type: "GET",
                dataType: "json",
                data: {
                    _xsrf: a
                },
                complete: SettingsProfile.Get.response
            })
        }
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "SettingsProfile.Get.response", "There was a problem. Please try again.");
        if (c.success == true) {
            var a = Templates.settings_profile({
                user: c.json.user
            });
            jQuery("#settings_middle").html(a);
            jQuery("#setting_profile_save_button").bind("click", SettingsProfile.Button.click)
        }
    }
};
SettingsProfile.Button = {
    count: 0,
    click: function(h) {
        SettingsProfile.Button.count = 0;
        var f = {};
        var g = false;
        var d = jQuery("#settings_profile_name").attr("value");
        f.name = d;
        g = true;
        var a = jQuery("#settings_profile_location").attr("value");
        if (Utils.HasValue(a) == true) {
            f.location = a;
            g = true
        }
        var c = jQuery("#settings_profile_website").attr("value");
        if (Utils.HasValue(c) == true) {
            c = Utils.AddHttp(c);
            f.website = c;
            g = true
        }
        var b = jQuery("#settings_profile_bio").attr("value");
        if (Utils.HasValue(b) == true) {
            f.bio = b;
            g = true
        }
        if (g == true) {
            SettingsProfile.Button.start();
            SettingsProfile.Set.request(f)
        }
        SettingsProfile.Avatar.sending = jQuery("#settings_profile_uploader_iframe")[0].contentWindow.Uploader.Request();
        if (SettingsProfile.Avatar.sending == true) {
            SettingsProfile.Button.start();
            SettingsProfile.Button.count++
        }
    },
    start: function() {
        jQuery("#setting_profile_save_green_check").removeClass("green_check");
        jQuery("#setting_profile_save_green_check").removeClass("display_none");
        jQuery("#setting_profile_save_button").addClass("inactive");
        jQuery("#setting_profile_save_button").unbind("click", SettingsProfile.Button.click)
    },
    done: function(b, a) {
        SettingsProfile.Button.count--;
        if (SettingsProfile.Button.count == 0) {
            jQuery("#setting_profile_save_button").addClass("done").removeClass("inactive");
            jQuery("#setting_profile_save_button").bind("click", SettingsProfile.Button.click);
            if (b == true) {
                jQuery("#setting_profile_save_green_check").addClass("green_check");
                jQuery(window).trigger({
                    type: "lalaProfileSet",
                    user: a,
                    success: true
                })
            } else {
                jQuery("#setting_profile_save_green_check").addClass("display_none");
                jQuery("#setting_profile_save_green_check").removeClass("green_check");
                alert("There was a problem. Please try again")
            }
        }
    }
};
SettingsProfile.Set = {
    method: player_root + "more.php?t=settings&action=profile",
    request: function(b) {
        SettingsProfile.Button.start();
        var a = Utils.get_cookie("_xsrf");
        b._xsrf = a;
        jQuery.ajax({
            url: SettingsProfile.Set.method,
            type: "POST",
            dataType: "json",
            data: b,
            complete: SettingsProfile.Set.response
        });
        SettingsProfile.Button.count++
    },
    response: function(a, c) {
        var b = Utils.APIResponse(a, "SettingsProfile.Set.response");
        if (b.success == true) {
            SettingsProfile.Button.done(true, b.json.user)
        } else {
            SettingsProfile.Button.done(false, null)
        }
    }
};
SettingsProfile.Avatar = {
    sending: false,
    done: function(c, a, b) {
        SettingsProfile.Avatar.sending = false;
        SettingsProfile.Button.done(c, a);
        if (c == true) {
            jQuery(window).trigger({
                type: "lalaAvatarSet"
            })
        }
    }
};
jQuery(window).bind("lalaSettingsProfile", SettingsProfile.Build);
if (typeof (SettingsConnections) == "undefined") {
    SettingsConnections = {}
}
SettingsConnections.Build = function() {
    var a = Templates.settings_connections();
    jQuery("#settings_middle").append(a);
    Utils.ShowLoading("#settings_connections_items");
    SettingsConnections.Get.request()
};
SettingsConnections.Services = {
    twitter: {
        url: "http://twitter.com",
        description: "чтобы твитить песни и найти друзей."
    }
    ,
    facebook: {
        url: "http://facebook.com",
        description: "чтобы делиться песнями на стене и найти друзей."
    }
};
SettingsConnections.Get = {
    method: player_root + "more.php?t=settings&action=services",
    request: function() {
        if (loggedInUser != null) {
            jQuery.ajax({
                url: SettingsConnections.Get.method,
                type: "GET",
                dataType: "json",
                complete: SettingsConnections.Get.response
            })
        }
    },
    response: function(e, h) {
        var g = Utils.APIResponse(e, "SettingsConnections.Get.response", "There was a problem. Please try again.");
        var c = false;
        if (g.success == true) {
            var f = Templates.settings_connections_item;
            html = "";
            for (var b in SettingsConnections.Services) {
                var a = null;
                if (g.json.services[b]) {
                    a = g.json.services[b];
                    if (a.last_refresh == null) {
                        c = true
                    }
                }
                var d = false;
                _.each(loggedInUser.import_feeds, function(i) {
                });
                html += f({
                    name: b,
                    details: SettingsConnections.Services[b],
                    service: a,
                    import_feed: d
                })
            }
            jQuery("#settings_connections_items").html(html)
        }
        if (c == true) {
            SettingsConnections.NeedsRefresh.request()
        }
    }
};
SettingsConnections.NeedsRefresh = {
    needs: true,
    method: player_root + "more.php?t=settings&refresh",
    request: function() {
        if (SettingsConnections.NeedsRefresh.needs == true) {
            SettingsConnections.NeedsRefresh.needs = false;
            jQuery.ajax({
                url: SettingsConnections.NeedsRefresh.method,
                type: "POST",
                dataType: "json",
                complete: SettingsConnections.NeedsRefresh.response
            })
        }
    },
    response: function(a, c) {
        var b = Utils.APIResponse(a, "SettingsConnections.NeedsRefresh.response");
        if (b.json.refreshed == true) {
            SettingsFriends.MaybeFriends.request()
        }
    }
};
SettingsConnections.ImportFeed = {
    add: {
        method: "/api/v3/settings/add-import-feed",
        request: function(b, a, c) {
            jQuery.ajax({
                url: SettingsConnections.ImportFeed.add.method,
                type: "POST",
                dataType: "json",
                data: {
                    url: b,
                    name: a,
                    type: c
                },
                complete: SettingsConnections.ImportFeed.add.response
            })
        },
        response: function(a, c) {
            var b = Utils.APIResponse(a, "SettingsConnections.ImportFeed.add.response", "There was a problem. Please try again.");
            jQuery(window).trigger({
                type: "lalaGetMe"
            })
        }
    },
    remove: {
        method: "/api/v3/settings/remove-import-feed/",
        request: function(a) {
            a = encodeURIComponent(a);
            jQuery.ajax({
                url: SettingsConnections.ImportFeed.remove.method + a,
                type: "POST",
                dataType: "json",
                complete: SettingsConnections.ImportFeed.remove.response
            })
        },
        response: function(a, c) {
            var b = Utils.APIResponse(a, "SettingsConnections.ImportFeed.remove.response", "There was a problem. Please try again.");
            jQuery(window).trigger({
                type: "lalaGetMe"
            })
        }
    }
};
SettingsConnections.Add = {
    click: function(b) {
        jQuery(this).css("opacity", 0.4);
        var a = jQuery(this).attr("service");
        jQuery(window).bind("lalaServiceConnectionAdd", this, SettingsConnections.Add.render);
        window.open("/connect/" + a)
    },
    listener: function(a) {
        jQuery(window).trigger({
            type: "lalaServiceConnectionAdd",
            obj: a
        })
    },
    render: function(c) {
        jQuery(window).unbind("lalaServiceConnectionAdd", SettingsConnections.Add.render);
        var b = c.obj;
        if (b.success == true) {
            var a = Templates.settings_connections_item({
                name: b.type,
                details: SettingsConnections.Services[b.type],
                service: b,
                import_feed: false
            });
            jQuery("#connection_item_" + b.type).replaceWith(a);
            SettingsFriends.MaybeFriends.request()
        } else {
            jQuery(c.data).css("opacity", 1);
            alert(b.response.message)
        }
    }
};
SettingsConnections.Remove = {
    method: player_root + "more.php?t=settings&action=services&social=%service%&force=remove",
    click: function(b) {
        jQuery(this).css("opacity", 0.4);
        var a = jQuery(this).attr("service");
        SettingsConnections.Remove.request(a, this)
    },
    request: function(a, b) {
        var c = SettingsConnections.Remove.method.replace("%service%", a);
        jQuery.ajax({
            url: c,
            type: "POST",
            dataType: "json",
            complete: SettingsConnections.Remove.response,
            context: b
        })
    },
    response: function(a, d) {
        jQuery(this).css("opacity", 1);
        var c = Utils.APIResponse(a, "SettingsConnections.Remove.response", "There was a problem removing this connection. Please try again.");
        if (c.success == true) {
            var b = c.json.removed;
            SettingsConnections.Remove.reset(b)
        }
    },
    reset: function(a) {
        var b = Templates.settings_connections_item({
            name: a,
            details: SettingsConnections.Services[a],
            service: null
        });
        jQuery("#connection_item_" + a).replaceWith(b)
    }
};
jQuery(window).bind("lalaSettingsConnections", SettingsConnections.Build);
jQuery(".connection_login_button").live("click", SettingsConnections.Add.click);
jQuery(".connection_logout_button").live("click", SettingsConnections.Remove.click);
if (typeof (SettingsFriends) == "undefined") {
    SettingsFriends = {}
}
SettingsFriends.Build = function() {
    jQuery(".settings_tab").removeClass("selected");
    jQuery("#settings_social").addClass("selected");
    var a = Templates.settings_friends();
    jQuery("#settings_middle").html(a);
    jQuery("#settings_bottom").html("");
    jQuery("#find_friends_form").bind("submit", SettingsFriends.Search.submit);
    SettingsFriends.MaybeFriends.request()
};
SettingsFriends.Search = {
    method: player_root + "more.php?t=settings&action=search&q=",
    submit: function() {
        var a = jQuery("#find_friends_input").attr("value");
        if (a != "") {
            SettingsFriends.Search.request(a)
        }
        return false
    },
    request: function(b) {
        Utils.ShowLoading("#find_friends_results");
        var a = Utils.get_cookie("_xsrf");
        jQuery.ajax({
            url: SettingsFriends.Search.method + b,
            type: "GET",
            dataType: "json",
            data: {
                _xsrf: a
            },
            complete: SettingsFriends.Search.response,
            cache: false
        })
    },
    response: function(a, c) {
        jQuery("#find_friends_results").empty();
        jQuery("#settings_friends_header").text("Search Results");
        var b = Utils.APIResponse(a, "SettingsFriends.Search.response", "There was a problem. Please try again.");
        if (b.success == true) {
            SettingsFriends.BuildFriends(b.json.users)
        }
    }
};
SettingsFriends.MaybeFriends = {
    method: player_root + "more.php?t=profile&username=%user%&action=maybe-friends",
    request: function(a) {
        Utils.ShowLoading("#find_friends_results");
        var b = SettingsFriends.MaybeFriends.method.replace("%user%", loggedInUser.username);
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            complete: SettingsFriends.MaybeFriends.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "SettingsFriends.MaybeFriends.response");
        if (c.success == true) {
            var a = c.json.users.length;
            if (a > 0) {
                jQuery("#find_friends_results").empty();
                //jQuery("#settings_friends_header").text("Friend suggestions");
                SettingsFriends.BuildFriends(c.json.users);
                SettingsConnections.Build()
            } else {
                SettingsFriends.Tastemakers.request()
            }
        }
    }
};
SettingsFriends.BuildFriends = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.common_users;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                user: b,
                position: c
            })
        }
    } else {
        d = '<div id="find_friends_none">No users found</div>'
    }
    jQuery("#find_friends_results").html(d)
};
SettingsFriends.Tastemakers = {
    method: player_root + "more.php?t=settings&action=tastemakers&extras=following",
    request: function(a) {
        jQuery.ajax({
            url: SettingsFriends.Tastemakers.method,
            type: "GET",
            dataType: "json",
            complete: SettingsFriends.Tastemakers.response,
            cache: false
        })
    },
    response: function(a, d) {
        var b = Utils.APIResponse(a, "SettingsFriends.MaybeFriends.response");
        jQuery("#find_friends_results").empty();
        if (b.success == true) {
            var c = Utils.Shuffle(b.json.following).slice(0, 5);
            jQuery("#settings_friends_header").text("Эти люди знают толк  в музыке. Подпишитесь на них.");
            SettingsFriends.BuildFriends(c);
            SettingsConnections.Build()
        }
    }
};
jQuery(window).bind("lalaSettingsSocial", SettingsFriends.Build);

if (typeof (SettingsDesign) == "undefined") {
    SettingsDesign = {}
}
SettingsDesign.Build = function() {
    jQuery(".settings_tab").removeClass("selected");
    jQuery("#settings_design").addClass("selected");
    jQuery("#settings_middle").html("");
    jQuery("#settings_bottom").html("");
    SettingsDesign.Get.request()
};
SettingsDesign.Get = {
    method: player_root + "more.php?t=member&username=",
    request: function() {
        if (loggedInUser != null) {
            Utils.ShowLoading("#settings_middle");
            var a = Utils.get_cookie("_xsrf");
            var b = SettingsDesign.Get.method + loggedInUser.username;
            jQuery.ajax({
                url: b,
                type: "GET",
                dataType: "json",
                data: {
                    _xsrf: a
                },
                complete: SettingsDesign.Get.response
            })
        }
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "SettingsDesign.Get.response", "There was a problem. Please try again.");
        if (c.success == true) {
            var a = Templates.settings_design({
                background: c.json.user.background
            });
            jQuery("#settings_middle").html(a);
            jQuery("#settings_design_save_button").bind("click", SettingsDesign.Button.click);
            jQuery("#settings_design_image").bind("keyup", SettingsDesign.Input.img);
            jQuery("#settings_design_repeat").bind("click", SettingsDesign.Input.repeat);
            jQuery(".settings_design_image_position_radio").bind("click", SettingsDesign.Input.position);
            jQuery("#settings_design_color").bind("keyup", SettingsDesign.Input.color)
        }
    }
};
SettingsDesign.Input = {
    img: function() {
        var a = jQuery("#settings_design_image").attr("value");
        SettingsDesign.Change.img(a)
    },
    repeat: function() {
        if (jQuery(this).is(":checked") == true) {
            SettingsDesign.Change.repeat("repeat")
        } else {
            SettingsDesign.Change.repeat("no-repeat")
        }
    },
    position: function() {
        if (jQuery("#settings_design_image_position_left_top").is(":checked") == true) {
            SettingsDesign.Change.position("left top")
        }
        if (jQuery("#settings_design_image_position_center_top").is(":checked") == true) {
            SettingsDesign.Change.position("center top")
        }
        if (jQuery("#settings_design_image_position_right_top").is(":checked") == true) {
            SettingsDesign.Change.position("right top")
        }
    },
    color: function() {
        var a = jQuery("#settings_design_color").attr("value");
        SettingsDesign.Change.color(a)
    }
};
SettingsDesign.Button = {
    click: function(c) {
        var a = {};
        var b = false;
        a.image = jQuery("#settings_design_image").attr("value");
        if (a.image != "") {
            a.use_image = "true"
        } else {
            a.image = null;
            a.use_image = "false"
        }
        a.color = jQuery("#settings_design_color").attr("value");
        if (Utils.HasValue(a.color) == false) {
            a.color = "#FFFFFF"
        }
        a.repeat = "no-repeat";
        if (jQuery("#settings_design_repeat").is(":checked") == true) {
            a.repeat = "repeat"
        }
        a.position = "left to";
        if (jQuery("#settings_design_image_position_left_top").is(":checked") == true) {
            a.position = "left top"
        }
        if (jQuery("#settings_design_image_position_center_top").is(":checked") == true) {
            a.position = "center top"
        }
        if (jQuery("#settings_design_image_position_right_top").is(":checked") == true) {
            a.position = "right top"
        }
        SettingsDesign.Button.start();
        SettingsDesign.Set.request(a)
    },
    start: function() {
        jQuery("#settings_design_save_green_check").removeClass("green_check");
        jQuery("#settings_design_save_green_check").removeClass("display_none");
        jQuery("#settings_design_save_button").addClass("inactive");
        jQuery("#settings_design_save_button").unbind("click", SettingsDesign.Button.click)
    },
    done: function(b, a) {
        jQuery("#settings_design_save_button").addClass("done").removeClass("inactive");
        jQuery("#settings_design_save_button").bind("click", SettingsDesign.Button.click);
        if (b == true) {
            userBackground = a.background;
            Utils.SetUserBackground("#right", userBackground);
            jQuery("#settings_design_save_green_check").addClass("green_check");
            jQuery(window).trigger({
                type: "lalaBackgroundDesignSet",
                user: a,
                success: true
            })
        } else {
            jQuery("#settings_design_save_green_check").addClass("display_none");
            jQuery("#settings_design_save_green_check").removeClass("green_check");
            alert("There was a problem. Please try again")
        }
    }
};
SettingsDesign.Change = {
    img: function(a) {
        jQuery("#right").css({
            "background-image": "url(" + a + ")"
        })
    },
    repeat: function(a) {
        jQuery("#right").css({
            "background-repeat": a
        })
    },
    position: function(a) {
        jQuery("#right").css({
            "background-position": a
        })
    },
    color: function(a) {
        jQuery("#right").css({
            "background-color": a
        })
    }
};
SettingsDesign.Set = {
    method: player_root + "more.php?t=settings&action=background",
    request: function(b) {
        var a = Utils.get_cookie("_xsrf");
        b._xsrf = a;
        jQuery.ajax({
            url: SettingsDesign.Set.method,
            type: "POST",
            dataType: "json",
            data: b,
            complete: SettingsDesign.Set.response
        })
    },
    response: function(a, c) {
        var b = Utils.APIResponse(a, "SettingsDesign.Set.response");
        if (b.success == true) {
            SettingsDesign.Button.done(true, b.json.user)
        } else {
            SettingsDesign.Button.done(false, null)
        }
    }
};
SettingsDesign.Themes = {
    click: function() {
        var d = jQuery(this).attr("theme");
        SettingsDesign.Change.img(player_root + "assets/images/backgrounds/" + d);
        jQuery("#settings_design_image").attr("value", "http://" + location.host + "/assets/images/backgrounds/" + d);
        var c = jQuery(this).attr("repeat");
        SettingsDesign.Change.repeat(c);
        if (c == "repeat") {
            jQuery("#settings_design_repeat").attr("checked", "true")
        } else {
            jQuery("#settings_design_repeat").removeAttr("checked")
        }
        var a = jQuery(this).attr("position");
        SettingsDesign.Change.position(a);
        if (a == "left top") {
            jQuery("#settings_design_image_position_left_top").attr("checked", "true")
        }
        if (a == "center top") {
            jQuery("#settings_design_image_position_center_top").attr("checked", "true")
        }
        if (a == "right top") {
            jQuery("#settings_design_image_position_right_top").attr("checked", "true")
        }
        var b = jQuery(this).attr("color");
        jQuery("#settings_design_color").attr("value", b);
        SettingsDesign.Change.color(b);
        jQuery(".settings_profile_theme").removeClass("active");
        jQuery(this).addClass("active")
    }
};
jQuery(window).bind("lalaSettingsDesign", SettingsDesign.Build);
jQuery(".settings_profile_theme").live("click", SettingsDesign.Themes.click);
if (typeof (SettingsAccount) == "undefined") {
    SettingsAccount = {}
}
SettingsAccount.Build = function() {
    jQuery(".settings_tab").removeClass("selected");
    jQuery("#settings_account").addClass("selected");
    var a = Templates.settings_account();
    jQuery("#settings_middle").html(a);
    jQuery("#settings_account_save_button").bind("click", SettingsAccount.Button.click);
//    jQuery("#settings_account_form").bind("submit", SettingsAccount.Button.click);
    SettingsAccount.Email.get.request()
};
SettingsAccount.Button = {
    count: 0,
    click: function(g) {
        SettingsAccount.Button.count = 0;
        var d = {};
        var b = false;
        var f = false;
        var c = false;
        var h = jQuery("#settings_account_email").attr("old_value");
        var j = jQuery("#settings_account_email").attr("value");
        if (j != h) {
            b = true;
            f = true
        }
        var i = jQuery("#settings_account_password").attr("value");
        var a = jQuery("#settings_account_password_verify").attr("value");
        if (i != "") {
            if (i == a) {
//                currentPassword = prompt("Please enter your current password");
                var currentPassword = jQuery("#settings_account_password_current").attr("value");
                if (currentPassword != null) {
                    b = true;
                    c = true
                } else {
                    b = false
                }
            } else {
                b = false;
                alert("New password and Retype password must be same")
            }
        }
        if (b == true) {
            SettingsAccount.Button.start();
            if (c == true) {
                SettingsAccount.Button.count++;


                SettingsAccount.Password.set.request(currentPassword, i, a)
            }
            if (f == true) {
                SettingsAccount.Button.count++;
                SettingsAccount.Email.set.request(j)
            }
        }
        return false
    },
    start: function() {
        jQuery("#settings_account_save_green_check1").removeClass("green_check");
        jQuery("#settings_account_save_green_check1").removeClass("display_none");
        jQuery("#settings_account_save_button").addClass("inactive");
        jQuery("#settings_account_save_button").unbind("click", SettingsAccount.Button.click)
    },
    done: function(a) {
//        alert(a);
        SettingsAccount.Button.count--;
        if (SettingsAccount.Button.count == 0) {
            jQuery("#settings_account_save_button").addClass("done").removeClass("inactive");
            jQuery("#settings_account_save_button").bind("click", SettingsAccount.Button.click);
            if (a == true) {
//                alert("test");
                jQuery('#pdmessage').show();
                jQuery('#pdmessage').css('color', 'green');
                jQuery('#pdmessage').html("Изменено успешно");

//                alert("Changed successfully");
                jQuery("#settings_account_save_green_check1").addClass("green_check");
                jQuery(window).trigger({
                    type: "lalaAccountSet",
                    success: true
                })
            } else {
                jQuery("#settings_account_save_green_check1").addClass("display_none");
                jQuery("#settings_account_save_green_check1").removeClass("green_check");
                alert("There was a problem saving your data. Please try again")
            }
        }
    }
};
SettingsAccount.Email = {
    method: player_root + "more.php?t=settings&action=email",
    get: {
        request: function() {
            if (loggedInUser != null) {
                var a = Utils.get_cookie("_xsrf");
                jQuery.ajax({
                    url: SettingsAccount.Email.method,
                    type: "GET",
                    dataType: "json",
                    data: {
                        _xsrf: a
                    },
                    complete: SettingsAccount.Email.get.response
                })
            }
        },
        response: function(a, c) {
            var b = Utils.APIResponse(a, "SettingsAccount.Email.get.response", "There was a problem. Please try again.");
            if (b.success == true) {
                SettingsAccount.Email.build(b.json.user)
            }
        }
    },
    set: {
        request: function(a) {
            var b = Utils.get_cookie("_xsrf");

            jQuery.ajax({
                url: SettingsAccount.Email.method,
                type: "POST",
                dataType: "json",
                data: {
                    email: a,
                    _xsrf: b
                },
                complete: SettingsAccount.Email.set.response
            })
        },
        response: function(a, c) {
            var b = Utils.APIResponse(a, "SettingsAccount.Emai.set.response");
            if (b.success == true) {
                SettingsAccount.Email.build(b.json.user)
            }
            SettingsAccount.Button.done(b.success)
        }
    },
    build: function(a) {
        jQuery("#settings_account_email").attr("old_value", a.email);
        jQuery("#settings_account_email").attr("value", a.email)
    }
};
SettingsAccount.Password = {
    method: player_root + "more.php?t=settings&action=password",
    set: {
        request: function(d, c, a) {
            var b = Utils.get_cookie("_xsrf");

            jQuery.ajax({
                url: SettingsAccount.Password.method,
                type: "POST",
                dataType: "json",
                data: {
                    password: d,
                    new_password: c,
                    confirm_new_password: a,
                    _xsrf: b
                },
                complete: SettingsAccount.Password.set.response
            })
        },
        response: function(a, c) {
            //console.log(a);
            var b = Utils.APIResponse(a, "SettingsAccount.Password.set.response");
//            alert(b);
            if (b) {
                SettingsAccount.Button.done(b.success)
            } else {
                 jQuery('#pdmessage').show();
                jQuery('#pdmessage').css('color', 'red');
                jQuery('#pdmessage').html("Old password is incorrect"); 
                location.reload();
            }
        }
    },
    build: function(a) {
        jQuery("#settings_account_email").attr("old_value", a.email);
        jQuery("#settings_account_email").attr("value", a.email)
    }
};
jQuery(window).bind("lalaSettingsAccount", SettingsAccount.Build);
var SettingsNotificationsView = Backbone.View.extend({
    el: $("#settings_middle"),
    template: Templates.settings_notifications,
    events: {
        "click #settings_notifications_desktop_new_song": "onDesktopNewSongClicked",
        "click #settings_notifications_desktop_love": "onDesktopLoveClicked",
        "click #settings_notifications_desktop_follower": "onDesktopFollowerClicked"
    },
    initialize: function() {
        _.bindAll(this, "render");
        jQuery(".settings_tab").removeClass("selected");
        jQuery("#settings_notifications").addClass("selected");
        this.render()
    },
    render: function() {
        var d = Storage.Get("show_song_desktop_notification");
        var c = Storage.Get("show_love_desktop_notification");
        var b = Storage.Get("show_follower_desktop_notification");
        var a = false;
        if (navigator.userAgent.toLowerCase().indexOf("chrome") != -1) {
            a = true
        }
        $(this.el).html(this.template({
            show_song_desktop_notification: d,
            show_love_desktop_notification: c,
            show_follower_desktop_notification: b,
            notifications_capable: a
        }))
    },
    onDesktopNewSongClicked: function() {
        if ($("#settings_notifications_desktop_new_song").is(":checked") == true) {
            Storage.Set("show_song_desktop_notification", true);
            try {
                if (webkitNotifications.checkPermission() != 0) {
                    webkitNotifications.requestPermission()
                }
            } catch (a) {
            }
        } else {
            Storage.Set("show_song_desktop_notification", false)
        }
    },
    onDesktopLoveClicked: function() {
        if ($("#settings_notifications_desktop_love").is(":checked") == true) {
            Storage.Set("show_love_desktop_notification", true);
            try {
                if (webkitNotifications.checkPermission() != 0) {
                    webkitNotifications.requestPermission()
                }
            } catch (a) {
            }
        } else {
            Storage.Set("show_love_desktop_notification", false)
        }
    },
    onDesktopFollowerClicked: function() {
        if ($("#settings_notifications_desktop_follower").is(":checked") == true) {
            Storage.Set("show_follower_desktop_notification", true);
            try {
                if (webkitNotifications.checkPermission() != 0) {
                    webkitNotifications.requestPermission()
                }
            } catch (a) {
            }
        } else {
            Storage.Set("show_follower_desktop_notification", false)
        }
    }
});
(function() {
    var c = SongView.extend({
        tagName: "div",
        className: "trending_song a_song",
        section: "trending",
        template: Templates.trending,
        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
            return this
        }
    });
    var b = Backbone.View.extend({
        el: $("#song_list"),
        section: "trending",
        initialize: function() {
            _.bindAll(this, "render", "onPlayAllClicked");
            Utils.HideSections("#song_list");
            $("#right").css("background", "#141318");
            $(this.el).html('<div id="trending_song_list"></div>');
            var trending_list = "";
            for (var i = 0; i < genre_list.length; i++) {
                trending_list += '<li class="trending_list_item trending_list_' + genre_list[i].replace(' ', '_') + '"><a href="/trending/' + genre_list[i].replace(' ', '+') + '">' + genre_list[i] + '</a></li>';
            }
            $(this.el).prepend('<div id="trending_top"><div id="trending_selector"><div id="trending_list" class=""><ul id="trending_list_tags"><li class="trending_list_item trending_list_overall"><a href="/trending/">ОБЩЕЕ</a></li> ' + trending_list + ' </ul></div></div><div id="trending_play_all_button"></div><div><span>Популярное</span> / <span id="trending_selected_input_wrapper"><span id="trending_selected"></span><input id="trending_selector_input" type="text" disabled></span></div></div<div id="trending_song_list"></div>');
            Utils.ShowLoading("#trending_song_list");
            var i = location.href.lastIndexOf("trending");
            var j = location.href.substring(i + 9);
            this.songs = new TrendingSongCollection({
                results: 19,
                path: "trending",
                genre: j
            });
            if (j) {
                $("#trending_selector_input").val(j);
            } else {
                $("#trending_selector_input").val("Общее");
            }
            this.songs.bind("reset", this.render);
            this.songs.fetch();
            jQuery("#trending_play_all_button").bind("click", this.onPlayAllClicked);
            $("#trending_list").css({
                height: "auto",
                opacity: "0"
            });
            var h = $("#trending_list").height();
            $("#trending_list").css({
                height: "0",
                opacity: "1"
            }).addClass("ready");
            $(".trending_list_" + d).addClass("selected");
            $("#trending_selected_input_wrapper").hover(function() {
                $("#trending_list").css("height", h).addClass("hover");
            });
            $("#trending_top").hover(null, function() {
                $("#trending_list").css("height", 0).removeClass("hover")
            });
            $trending_input = $("#trending_selector_input");
            $trending_selected = $("#trending_selected");
            var b;
            var k = 0;
            var a = $trending_selected.text();
            $trending_input.focus(function() {
                $trending_selected.addClass("focus")
            }).blur(function() {
                $trending_selected.text(a).removeClass("focus");
                $trending_input.val("")
            });
            $trending_input.keydown(function(l) {
                if (l.keyCode == 9 || l.keyCode == 39 || l.keyCode == 40) {
                    $trending_input.val(b.tags[k % b.tags.length]);
                    $trending_selected.text($trending_input.val());
                    if (l.keyCode == 9 || l.keyCode == 40) {
                        k++
                    }
                    return false
                }
                if (l.keyCode == 13) {
                    $(window).trigger({
                        type: "lalaNeedHistoryChange",
                        href: "trending/" + $.trim($trending_input.val().toLowerCase())
                    });
                    return false
                }
            })
        },
        render: function() {
            $("#right").attr("class", "trending");
            var h = 320,
                    k = $("#right"),
                    m = k.width() - d,
                    l = Math.ceil(m / h),
                    n = Math.floor(m / l);
            $("#trending_song_list").html(this.songs.map(function(u, v) {
                var A = new c({
                    model: u
                });
                var q = "";
                var y = u.collection.at(v);
                var z = y.get("artist_image").extralarge.width;
                var s = y.get("artist_image").extralarge.height;
                var p = n + 2;
                var o;
                if (y.get("artist_image").extralarge.src != player_root + "assets/images/album_320x320.png") {
                    o = y.get("artist_image").extralarge.src.replace("252", "500")
                } else {
                    $(A.el).addClass("no_art");
                    o = y.get("image").large
                }
                var w, t;
                if (z > s) {
                    w = "auto " + p + "px";
                    t = "50% -1px"
                } else {
                    w = p + "px auto";
                    t = "-1px -1px"
                }
                q += "background-image:url(" + o + ");";
                q += "background-size:" + w + ";";
                q += "background-position:" + t + ";";
                A.render();
                var B = "width:" + n + "px;height:" + n + "px;";
                $(A.el).attr("style", B);
                $(A.el).find(".trending_song_bg").attr("style", q);
                var r = y.get("id");
                $(A.el).addClass("a_song_" + r);
                try {
                    if (AudioPlayer.List.current[AudioPlayer.QueueNumber].id == r) {
                        $(A.el).addClass("playing")
                    }
                } catch (x) {
                }
                return A.el
            }));
            var g = $(".trending_song").length;
            var j = 0;

            function f() {
                $(".trending_song").eq(j).removeClass("off");
                if (j < g) {
                    j++;
                    setTimeout(f, 60)
                }
            }
        },
        onPlayAllClicked: function() {
            $(window).trigger({
                type: "lalaNewSongList",
                list: this.songs.toJSON(),
                position: 0,
                section: this.section
            });
            return false
        }
    });
    var a = _.debounce(e, 100);
    var d = $.browser.webkit ? 10 : $.browser.mozilla ? 15 : 20;
    if (!$.browser.webkit) {
        $("#trending_top").css({
            right: 15
        });
        $("#right").css({
            "border-right": "none"
        });
        $("#left_drop, #left_following").css({
            width: 210
        })
    }

    function e() {
        if (this.Trending) {
            var p = 320,
                    r = $("#right"),
                    q = r.width() - d,
                    o = Math.ceil(q / p),
                    h = Math.floor(q / o);
            var j = this.Trending.songs.models;
            for (var l = 0; l < j.length; l++) {
                var x = j[l].attributes;
                var g = $(".trending_song").eq(l);
                var v = g.find(".trending_song_bg");
                var n = x.artist_image.extralarge.width;
                var k = x.artist_image.extralarge.height;
                var f = h + 2;
                var m = n > k ? "auto " + f + "px;" : f + "px auto;";
                var t = "";
                t += "background-image:" + v.css("backgroundImage") + ";";
                t += "background-position:" + v.css("backgroundPosition") + ";";
                t += "background-size:" + m;
                var u = "width:" + h + "px;";
                u += "height:" + h + "px;";
                g.attr("style", u);
                g.find(".trending_song_bg").attr("style", t)
            }
        }
    }
    $(window).resize(a);
    $(window).bind("lalaHistoryChange", function(f) {
        if (f.href.indexOf("trending") != -1) {
            var d = f.href.lastIndexOf("trending");
            window.Trending = new b
        }
    })
})();
(function() {
    var c = SongView.extend({
        tagName: "div",
        className: "trending_song a_song",
        section: "trending",
        template: Templates.trending,
        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
            return this
        }
    });
    var b = Backbone.View.extend({
        el: $("#song_list"),
        section: "trending",
        initialize: function() {
            _.bindAll(this, "render", "onPlayAllClicked");
            Utils.HideSections("#song_list");
            $("#right").css("background", "#141318");
            $(this.el).html('<div id="trending_song_list"></div>');
            $(this.el).prepend('<div id="trending_top"><div id="trending_play_all_button"></div>New Songs</div>');
            Utils.ShowLoading("#trending_song_list");
            this.songs = new TrendingSongCollection({
                results: 19,
                path: "newsongs"
            });
            this.songs.bind("reset", this.render);
            this.songs.fetch();
            jQuery("#trending_play_all_button").bind("click", this.onPlayAllClicked)
        },
        render: function() {
            $("#right").attr("class", "trending");
            var h = 320,
                    k = $("#right"),
                    m = k.width() - d,
                    l = Math.ceil(m / h),
                    n = Math.floor(m / l);
            $("#trending_song_list").html(this.songs.map(function(u, v) {
                var A = new c({
                    model: u
                });
                var q = "";
                var y = u.collection.at(v);
                var z = y.get("artist_image").extralarge.width;
                var s = y.get("artist_image").extralarge.height;
                var p = n + 2;
                var o;
                if (y.get("artist_image").extralarge.src != player_root + "assets/images/album_320x320.png") {
                    o = y.get("artist_image").extralarge.src.replace("252", "500")
                } else {
                    $(A.el).addClass("no_art");
                    o = y.get("image").large
                }
                var w, t;
                if (z > s) {
                    w = "auto " + p + "px";
                    t = "50% -1px"
                } else {
                    w = p + "px auto";
                    t = "-1px -1px"
                }
                q += "background-image:url(" + o + ");";
                q += "background-size:" + w + ";";
                q += "background-position:" + t + ";";
                A.render();
                var B = "width:" + n + "px;height:" + n + "px;";
                $(A.el).attr("style", B);
                $(A.el).find(".trending_song_bg").attr("style", q);
                var r = y.get("id");
                $(A.el).addClass("a_song_" + r);
                try {
                    if (AudioPlayer.List.current[AudioPlayer.QueueNumber].id == r) {
                        $(A.el).addClass("playing")
                    }
                } catch (x) {
                }
                return A.el
            }));
            var g = $(".trending_song").length;
            var j = 0;

            function f() {
                $(".trending_song").eq(j).removeClass("off");
                if (j < g) {
                    j++;
                    setTimeout(f, 60)
                }
            }
        },
        onPlayAllClicked: function() {
            $(window).trigger({
                type: "lalaNewSongList",
                list: this.songs.toJSON(),
                position: 0,
                section: this.section
            });
            return false
        }
    });
    var a = _.debounce(e, 100);
    var d = $.browser.webkit ? 10 : $.browser.mozilla ? 15 : 20;
    if (!$.browser.webkit) {
        $("#trending_top").css({
            right: 15
        });
        $("#right").css({
            "border-right": "none"
        });
        $("#left_drop, #left_following").css({
            width: 210
        })
    }

    function e() {
        if (this.Trending) {
            var p = 320,
                    r = $("#right"),
                    q = r.width() - d,
                    o = Math.ceil(q / p),
                    h = Math.floor(q / o);
            var j = this.Trending.songs.models;
            for (var l = 0; l < j.length; l++) {
                var x = j[l].attributes;
                var g = $(".trending_song").eq(l);
                var v = g.find(".trending_song_bg");
                var n = x.artist_image.extralarge.width;
                var k = x.artist_image.extralarge.height;
                var f = h + 2;
                var m = n > k ? "auto " + f + "px;" : f + "px auto;";
                var t = "";
                t += "background-image:" + v.css("backgroundImage") + ";";
                t += "background-position:" + v.css("backgroundPosition") + ";";
                t += "background-size:" + m;
                var u = "width:" + h + "px;";
                u += "height:" + h + "px;";
                g.attr("style", u);
                g.find(".trending_song_bg").attr("style", t)
            }
        }
    }
    $(window).resize(a);
    $(window).bind("lalaHistoryChange", function(f) {
        if (f.href == "newsongs") {
            window.Trending = new b;
            $("#right").unbind("scrollBottom", this.onScrollBottom)
        }
    })
})();
(function() {
    var c = SongView.extend({
        tagName: "div",
        className: "trending_song a_song",
        section: "trending",
        template: Templates.trending,
        render: function() {
            $(this.el).html(this.template(this.model.toJSON()));
            return this
        }
    });
    var b = Backbone.View.extend({
        el: $("#song_list"),
        section: "trending",
        initialize: function() {
            _.bindAll(this, "render", "onPlayAllClicked");
            Utils.HideSections("#song_list");
            $("#right").css("background", "#141318");
            $(this.el).html('<div id="trending_song_list"></div>');
            $(this.el).prepend('<div id="trending_top"><div id="trending_play_all_button"></div>Новые песни</div>');
            Utils.ShowLoading("#trending_song_list");
            this.songs = new TrendingSongCollection({
                results: 19,
                path: "newrelease"
            });
            this.songs.bind("reset", this.render);
            this.songs.fetch();
            jQuery("#trending_play_all_button").bind("click", this.onPlayAllClicked);


            /*$(this.el).html(this.template({
             loggedInUser: loggedInUser
             selected: unescape(c)
             }));*/


            Utils.ShowLoading("#trending_song_list");
            this.songs.bind("reset", this.render);
            this.songs.fetch();
            jQuery("#trending_play_all_button").bind("click", this.onPlayAllClicked);
        },
        render: function() {
            $("#right").attr("class", "trending");
            var h = 320,
                    k = $("#right"),
                    m = k.width() - d,
                    l = Math.ceil(m / h),
                    n = Math.floor(m / l);
            $("#trending_song_list").html(this.songs.map(function(u, v) {
                var A = new c({
                    model: u
                });
                var q = "";
                var y = u.collection.at(v);
                var z = y.get("artist_image").extralarge.width;
                var s = y.get("artist_image").extralarge.height;
                var p = n + 2;
                var o;
                if (y.get("artist_image").extralarge.src != player_root + "assets/images/album_320x320.png") {
                    o = y.get("artist_image").extralarge.src.replace("252", "500")
                } else {
                    $(A.el).addClass("no_art");
                    o = y.get("image").large
                }
                var w, t;
                if (z > s) {
                    w = "auto " + p + "px";
                    t = "50% -1px"
                } else {
                    w = p + "px auto";
                    t = "-1px -1px"
                }
                q += "background-image:url(" + o + ");";
                q += "background-size:" + w + ";";
                q += "background-position:" + t + ";";
                A.render();
                var B = "width:" + n + "px;height:" + n + "px;";
                $(A.el).attr("style", B);
                $(A.el).find(".trending_song_bg").attr("style", q);
                var r = y.get("id");
                $(A.el).addClass("a_song_" + r);
                try {
                    if (AudioPlayer.List.current[AudioPlayer.QueueNumber].id == r) {
                        $(A.el).addClass("playing")
                    }
                } catch (x) {
                }
                return A.el
            }));
            var g = $(".trending_song").length;
            var j = 0;

            function f() {
                $(".trending_song").eq(j).removeClass("off");
                if (j < g) {
                    j++;
                    setTimeout(f, 60)
                }
            }
        },
        onPlayAllClicked: function() {
            $(window).trigger({
                type: "lalaNewSongList",
                list: this.songs.toJSON(),
                position: 0,
                section: this.section
            });
            return false
        }
    });
    var a = _.debounce(e, 100);
    var d = $.browser.webkit ? 10 : $.browser.mozilla ? 15 : 20;
    if (!$.browser.webkit) {
        $("#trending_top").css({
            right: 15
        });
        $("#right").css({
            "border-right": "none"
        });
        $("#left_drop, #left_following").css({
            width: 210
        })
    }

    function e() {
        if (this.Trending) {
            var p = 320,
                    r = $("#right"),
                    q = r.width() - d,
                    o = Math.ceil(q / p),
                    h = Math.floor(q / o);
            var j = this.Trending.songs.models;
            for (var l = 0; l < j.length; l++) {
                var x = j[l].attributes;
                var g = $(".trending_song").eq(l);
                var v = g.find(".trending_song_bg");
                var n = x.artist_image.extralarge.width;
                var k = x.artist_image.extralarge.height;
                var f = h + 2;
                var m = n > k ? "auto " + f + "px;" : f + "px auto;";
                var t = "";
                t += "background-image:" + v.css("backgroundImage") + ";";
                t += "background-position:" + v.css("backgroundPosition") + ";";
                t += "background-size:" + m;
                var u = "width:" + h + "px;";
                u += "height:" + h + "px;";
                g.attr("style", u);
                g.find(".trending_song_bg").attr("style", t)
            }
        }
    }
    $(window).resize(a);
    $(window).bind("lalaHistoryChange", function(f) {
        if (f.href == "newrelease") {
            window.Trending = new b;
            $("#right").unbind("scrollBottom", this.onScrollBottom)
        }
    })
})();
var MeView = Backbone.View.extend({
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render");
        this.model.bind("change", this.render);
        this.model.fetch({
            error: this.options.error
        })
    },
    render: function() {
        loggedInUser = this.model.toJSON();
        userBackground = this.model.get("background");
        jQuery(window).trigger({
            type: "lalaMe",
            user: loggedInUser
        });
        return this
    }
});
jQuery(window).bind("lalaGetMe", function(c) {
    var b = new MeUser();
    var a = new MeView({
        model: b,
        error: c.error
    })
});


$('a.Download_songs').click(function() {
    var url = $(this).attr('id');
    var url1 = $(this).attr('data-title');
    var urlalbum = $(this).attr('data-album');
    var urlalbumid = $(this).attr('data-album_id');
    var urlimg = $(this).attr('data-image');
    var a = loggedInUser.user_id;

    if (loggedInUser.user_id != null) {
        jQuery(window).trigger({
            type: "laladownloadpayement",
            song_id: url,
            song_title: url1,
            song_album: urlalbum,
            song_album_id: urlalbumid,
            song_image: urlimg
        });

    } else {

        alert("You must login to use this feature!", true)
    }

});


if (typeof (DownloadPayement) == "undefined") {
    DownloadPayement = {}
}
DownloadPayement.Listen = function(a) {
    var b;
    var c;
    $.ajax({
        type: "GET",
        url: player_root + "more.php?t=price&action=all",
        success: function(d) {

            b = d.songprice.price;
            c = d.albumprice.price;
            //console.log(b);
            //console.log(c);
            DownloadPayement.Show(a, b, c)

        }
    });



};
DownloadPayement.InApp = true;
DownloadPayement.Song = null;
DownloadPayement.Value = "";
DownloadPayement.IsShowing = false;
DownloadPayement.Show = function(b, c, d) {
    if (DownloadPayement.IsShowing == false) {
        DownloadPayement.Song = b;
        jQuery("#full_cover").removeClass("display_none");
        var a = Templates.download_subscribe({
            shared: false,
            song: DownloadPayement.Song,
            songprice: c,
            albumprice: d
        });

        jQuery(document.body).append(a);
        DownloadPayement.AddListeners();
        DownloadPayement.IsShowing = true
    }
};
DownloadPayement.Hide = function() {
    jQuery("#download_box_close_button").unbind("click", DownloadPayement.Hide);
    jQuery("#download_box").remove();
    jQuery("#full_cover").addClass("display_none");
    DownloadPayement.Song = null;
    jQuery(window).trigger({
        type: "lalaShareBoxClose"
    });
    DownloadPayement.IsShowing = false
};
DownloadPayement.AddListeners = function() {
    jQuery("#download_box_close_button").bind("click", DownloadPayement.Hide);
    jQuery("#download_button").removeClass("inactive");

    $("a.Payement_download").click(function(b) {
        var song = $(this).attr('data-song');
//        console.log(song); 
        var data = song.split(',');
        var currenturl = window.location.href;

        $.ajax({
            type: "GET",
            data: {
                data: "song"
            },
            url: player_root + "more.php?t=price&action=specific",
            success: function(d) {

                d.price;
                if (d.price == 0) {
                    window.location.href = player_root + '/proxy.php?current_url=' + currenturl + '&song_id=' + data[0];
                } else {
                    window.open(player_root + 'PayPalcredits.php/PayPalCredit-php/paypal_ec_redirect.php?id=' + data[0] + '&title=' + data[1] + '&currenturl=' + currenturl + '&price=' + d.price);
                }
            }
        });

        //alert(player_root + 'PayPalcredits.php/PayPalCredit-php/paypal_ec_redirect.php?id=' +data[0]+ '&title='+data[1]);




    });
    $("a.Payment_album").click(function(b) {
        var urlalbum = $(this).attr('data-album');
        var album_info = urlalbum.split(',');
        //console.log(album_info);
        var currenturl = window.location.href;

        $.ajax({
            type: "GET",
            data: {
                data: "album"
            },
            url: player_root + "more.php?t=price&action=specific",
            success: function(d) {
                d.price;
                if (d.price == 0) {
                    window.location.href = player_root + '/PayAlbum.php?current_url=' + currenturl + '&album_id=' + album_info[1] + '&title=' + album_info[0];
                } else {
                    window.open(player_root + 'PayPalcredits.php/PayPalCredit-php/paypal_ec_redirect.php?album_id=' + album_info[1] + '&currenturl=' + currenturl + '&title=' + album_info[0] + '&price=' + d.price);
                }
            }
        });

    });
};
DownloadPayement.SongDownload = function(b) {
    if (b.success == true) {
        DownloadPayement.Hide();
        var src = b.song_id;
        var currenturl = window.location.href;
        window.location.href = player_root + '/proxy.php?current_url=' + currenturl + '&song_id=' + src;
    }
    else {
        DownloadPayement.Hide();
        alert("Payment Transaction failed try once again....");
    }
}
DownloadPayement.AlbumDownload = function(b) {
    if (b.success == true) {
        DownloadPayement.Hide();
        var id = b.album_id;
        var title = b.album_title;
        var currenturl = window.location.href;
        window.location.href = player_root + '/PayAlbum.php?current_url=' + currenturl + '&album_id=' + id + '&title=' + title;
    }
    else {
        DownloadPayement.Hide();
        alert("Payment Transaction failed try once again....");
    }
}

jQuery(window).bind("laladownloadpayement", DownloadPayement.Listen);




$(window).bind("lalaHistoryChange", function(f) {
    if (f.href == "radio") {

        Radio.Build();

    }
});
if (typeof (Radio) == "undefined") {
    Radio = {}
}
Radio.Build = function() {
    Utils.HideSections("#song_list");
    $("#right").css("background", "#141318");
    var b = Templates.radio_head();
    jQuery("#song_list").html(b);
    Radio.Display.request()
};
Radio.Display = {
    method: player_root + "more.php?t=radio",
    request: function() {

        var b = Radio.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            complete: Radio.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "Radio.Display.response");
        if (c.success == true) {
            Radio.BuildList(c.json.buffers);


        }
    }
};
Radio.BuildList = function(f) {

    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.radio_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    }

    $("#sites_rows").html(d);


}
jQuery(".radio_play_button").live("click", function() {

    var song = $(this).attr('data-song');
    //console.log(song);
    StationSongs.Build(song);

});
if (typeof (StationSongs) == "undefined") {
    StationSongs = {}
}
StationSongs.Build = function(e) {
    StationSongs.Display.request(e);
}
StationSongs.Display = {
    method: player_root + "more.php?t=station_songs",
    request: function(e) {
        var b = StationSongs.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                song_ids: e
            },
            complete: StationSongs.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "StationSongs.Display.response");
        if (c.success == true) {
            $(window).trigger({
                type: "lalaNewSongList",
                list: (c.json.buffers.songs),
                position: 0

            });

        }
    }
};



var SongPageView = SongView.extend({
    el: $("#song_list"),
    template: Templates.song_page,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render", "error");
        jQuery("#right").css("background", "#141318");
        Utils.HideSections("#song_list");
        Utils.ShowLoading("#song_list");
        this.model.bind("change", this.render);
        this.model.fetch({
            error: this.error
        })
    },
    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        $(this.el).prepend('');
        Utils.ShowLoading("#trending_song_list");
        try {
            jQuery(".a_song_" + AudioPlayer.List.current[AudioPlayer.QueueNumber].id).addClass("playing")
        } catch (a) {
        }
        $(this.el).find(".song_page_comment").find("a").attr({
            target: "_blank",
            outbound_type: "song_page_comment"
        });
        return this
    },
    error: function() {
        jQuery(window).trigger({
            type: "lalaShowErrorPage",
            el: this.el
        })
    },
    addGraph: function() {
        var h = this.toJSON();
        var f = $('<div id="loves_graph">');
        var n = $('<canvas id="loves_canvas">').attr({
            width: "780px",
            height: "240px"
        });
        $("#song_list").append(n);
        var m = n[0].getContext("2d");
        var d = 20;
        var k = 10;
        var l = 0;
        var b = 200;

        function c(g) {
            return g * Math.PI / 180
        }

        function j(x, B, r) {
            if (x.parents == undefined || x.parents == null) {
                x.parents = 0
            }
            var q = B == null ? f : B;
            var o = $('<div class="graph_node">');
            o.append('<div class="graph_user"><div class="graph_avatar" style="background-image:url(' + LALA_AVATAR_HOST + "avatar_medium_" + x.username + '.jpg);"></div><span>' + x.username + "</span></div>");
            q.append(o);
            var z = 1;
            var A = 0;
            var y = 100;
            var w = 0;
            var g = 0;
            if (r) {
                z = r.children.length;
                A = $(r.children).index(x);
                y = r.angleInc;
                w = r.angle;
                g = r.childNum
            }
            if (z <= 0) {
                var t = 100
            } else {
                var t = y / (z)
            }
            var s = w + (t * A) - (t * (z - 1)) / 2;
            x.angle = s;
            x.angleInc = t;
            x.r = x.parents;
            x.x = x.r * (d + k);
            x.px = Math.cos(c(s)) * x.x;
            x.py = Math.sin(c(s)) * x.x + 105;
            x.childNum = A;
            m.strokeStyle = "rgba(0,0,0,.1)";
            m.rect(x.px + b, x.py, d, d);
            m.stroke();
            var v = new Image();
            $(v).data({
                ring: l,
                children: z,
                childNum: A,
                obj: x
            });
            $(v).load(function(C) {
                var i = $(this);
                m.drawImage(v, i.data("obj").px + b, i.data("obj").py, d, d)
            });
            v.src = LALA_AVATAR_HOST + "avatar_small_" + x.username + ".jpg";
            if (x.children.length > 0) {
                l++;
                m.strokeStyle = "rgba(255,255,255,.1)";
                m.beginPath();
                m.arc((d / 2) + b, 120, l * (d + k), c(0), c(360), true);
                m.closePath();
                m.stroke();
                for (var u = 0; u < x.children.length; u++) {
                    var p = x.children[u];
                    p.parents = x.parents + 1;
                    j(p, o, x)
                }
            }
        }
        var a = [];
        for (var e in h) {
            if (h[e].children) {
                a.push(h[e])
            }
        }
        a.reverse();
        for (var e = 0; e < a.length; e++) {
            l = 0;
            j(a[e])
        }
        $("#song_list").append(f);
        return this
    }
});
var LovesGraphView = Backbone.View.extend({
    initialize: function() {
        _.bindAll(this, "render", "add")
    },
    render: function() {
        return this
    },
    add: function(a) {
    }
});
jQuery(window).bind("lalaHistoryChange", function(c) {
    var a = c.href.indexOf("song/");
    if (a != -1) {
        var f = c.href.substr(a + 5);
        $("#song_list").undelegate();
        var d = new Song({
            id: f
        });
        var b = new SongPageView({
            model: d,
            section: "song_page"
        });
        $("#right").unbind("scrollBottom", this.onScrollBottom)
    }
});
var ExploreView = Backbone.View.extend({
    el: $("#song_list"),
    template: Templates.explore,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render");
        $("#right").removeAttr("style");
        $("#right").attr("class", this.section);
        Utils.HideSections("#song_list");
        Utils.ShowLoading("#song_list");
        this.render()
    },
    render: function() {
        $(this.el).html(this.template({
            section: this.section
        }));
        $("#right").scrollTop(0);
        switch (this.section) {
            case "":
                $("#right").attr("class", "top-of-the-day");
                var c = new SOTDView({
                    el: "#explore"
                });
                $("#explore_nav_sotd").addClass("active");
                break;
            case "top-of-the-day":
                var c = new SOTDView({
                    el: "#explore"
                });
                $("#right").removeAttr("style");
                $("#right").attr("class", "top-of-the-week");
                $("#explore_nav_sotd").addClass("active");
                break;
            case "top-of-the-week":
                var c = new SOTDView({
                    el: "#explore"
                });
                $("#right").removeAttr("style");
                $("#right").attr("class", "top-of-the-week");
                $("#explore_nav_sotd").addClass("active");
                break;
            case "top-of-the-month":
                var c = new SOTDView({
                    el: "#explore"
                });
                $("#right").removeAttr("style");
                $("#right").attr("class", "top-of-the-week");
                $("#explore_nav_sotd").addClass("active");
                break;
            case "top-of-the-year":
                var c = new SOTDView({
                    el: "#explore"
                });
                $("#right").removeAttr("style");
                $("#right").attr("class", "top-of-the-week");
                $("#explore_nav_sotd").addClass("active");
                break;
            case "top-of-all-time":
                var c = new SOTDView({
                    el: "#explore"
                });
                $("#right").removeAttr("style");
                $("#right").attr("class", "top-of-the-week");
                $("#explore_nav_sotd").addClass("active");
                break;
            case "genres":
                var b = new ExploreGenres({
                    el: "#explore"
                });
                break;
            case "album-of-the-week":
                var d = new AOTWView({
                    el: "#explore"
                });
                break;
            case "tastemakers":
                var f = new TastemakerView({
                    el: "#explore"
                });
                break;
            case "latest":
                var e = new LatestLovedView();
                break;
            case "top-playlists":
                var b = new TopPlaylists({
                    el: "#explore"
                });
                break;
            case "top-artists":
                var b = new TopArtists({
                    el: "#explore"
                });
                break;
            case "top-albums":
                var b = new TopAlbums({
                    el: "#explore"
                });
                break;
            default:
                var a = new GenreSongView({
                    show_user: false,
                    genre: this.section,
                    show_user_in_others: true
                });
                $("#right").attr("class", "genres");
                break
        }
        jQuery("#left_row_explore").attr("href", "/explore/" + this.section);
        return this
    }
});
var TopArtists = Backbone.View.extend({
    template: Templates.explore_artists,
    playOnFetch: false,
    initialize: function() {
        $("#right").css("background-color", "#141318");
        this.render()
    },
    render: function() {
        this.genres = genre_list;
        $("#explore").append(this.template());
        ExploreTopList.Build("artist");
        return this
    }
});
var TopAlbums = Backbone.View.extend({
    template: Templates.explore_albums,
    playOnFetch: false,
    initialize: function() {
        $("#right").css("background-color", "#141318");
        this.render()
    },
    render: function() {
        this.genres = genre_list;
        $("#explore").append(this.template());
        ExploreTopList.Build("album");
        return this
    }
});
var TopPlaylists = Backbone.View.extend({
    template: Templates.explore_playlists,
    playOnFetch: false,
    initialize: function() {
        $("#right").css("background-color", "#141318");
        this.render()
    },
    render: function() {
        this.genres = genre_list;
        $("#explore").append(this.template());
        ExploreTopList.Build("playlist");
        return this
    }
});
if (typeof (ExploreTopList) == "undefined") {
    ExploreTopList = {}
}
ExploreTopList.Build = function(e) {
    ExploreTopList.Display.request(e)
};
ExploreTopList.Display = {
    method: player_root + "more.php?t=toplist",
    request: function(e) {
        var date = "the-week";
        var b = ExploreTopList.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                "type": e,
                "date": date

            },
            complete: ExploreTopList.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "ExploreTopList.Display.response");
        if (c.success == true) {
            ExploreTopList.BuildList(c.json.buffers);
        }
    }
};
ExploreTopList.BuildList = function(f) {
    var d = "";

    var a = f.length;
    if (a > 0) {
        var e = Templates.top_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div id="error_message">У вас нет ни одного альбома.</div>'
    }
    jQuery("#site_rows").html(d)
};
var TastemakerView = Backbone.View.extend({
    template: Templates.explore_tastemaker,
    initialize: function() {
        $("#explore").append(this.template());
        _.bindAll(this, "render");
        this.tastemakers = new TastemakersCollection({
            start: 0,
            results: 100
        });
        this.tastemakers.bind("reset", this.render);
        this.tastemakers.fetch();
        Utils.ShowLoading("#item_rows")
    },
    render: function() {
        var f = this.tastemakers.models;
        f = Utils.Shuffle(f);
        var e = Templates.explore_tastemaker_user;
        var d = "";
        for (var c = 0; c < 11; c++) {
            var a = f[c].toJSON();
            d += e(a)
        }
        $("#tastemaker_avatars").append(d);
        $("#item_rows").addClass("feed");
        var b = new TastemakerSongsView({
            username: "tastemakers",
            item_rows_classname: "feed"
        });
        return this
    }
});
var TastemakerSongsView = SongListView.extend({
    collectionClass: SongLovedFeedCollection,
    section: "explore_tastemaker",
    render: function() {
        return this
    }
});
var SOTDView = Backbone.View.extend({
    template: Templates.explore_sotd,
    initialize: function() {
        $("#explore").append(this.template());
        _.bindAll(this, "render");
        $("#trending_list").css({
            height: "auto",
            opacity: "0"
        });
        var h = $("#trending_list").height();
        $("#trending_list").css({
            height: "0",
            opacity: "1"
        }).addClass("ready");

        $(".explore_header").hover(function() {
            $("#trending_list").css("height", h).addClass("hover");
        });
        $(".explore_section_header").hover(null, function() {
            $("#trending_list").css("height", 0).removeClass("hover")
        });
        this.render()
    },
    render: function() {
        Utils.ShowLoading("#item_rows");
        var i = location.href.lastIndexOf("top-of-");
        var j = location.href.substring(i + 7);
        if (j == "the-week")
            $(".explore_header").text("Топ Неделю");
        else if (j == "the-day")
            $(".explore_header").text("Топ День");
        else if (j == "the-month")
            $(".explore_header").text("Топ Месяц");
        else if (j == "all-time")
            $(".explore_header").text("Топ Все");
        else if (j == "the-year")
            $(".explore_header").text("Топ Год");
        var a = new SOTDSongsView({
            show_user: false,
            date: j
        });
        return this
    }
});
var SOTDSongsView = SongListView.extend({
    collectionClass: TOPSongsCollection,
    section: "explore_sotd",
    render: function() {
        return this
    }
});
var SOTDCalendarView = Backbone.View.extend({
    template: Templates.explore_sotd_day,
    initialize: function() {
        _.bindAll(this, "render", "add");
        this.sites = this.options.sites.models;
        this.calendar_el = $(this.el);
        for (var b = 0; b < this.sites.length; b++) {
            var a = this.sites[b];
            this.calendar_el.append(this.template(a.toJSON()))
        }
    },
    render: function() {
    },
    add: function(a) {
    }
});
var AOTWView = Backbone.View.extend({
    template: Templates.explore_album_of_week,
    section: "explore_aotw",
    initialize: function() {
        $("#explore").append(this.template());
        $("#right").css("background", "#141318");
        _.bindAll(this, "render", "add", "onPlayAllButtonClicked");
        jQuery("#explore_play_button_album_of_week").bind("click", this.onPlayAllButtonClicked);
        this.albums = new AOTWCollection({
            start: 0,
            results: 1
        });
        this.albums.bind("reset", this.render);
        this.albums.fetch()
    },
    render: function() {
        if (this.albums.models[0]) {
            var b = this.albums.models[0];
            var c = b.get("url");
            var e = b.get("title");
            var a = b.get("artist");
            var d = b.get("artwork_url");
            jQuery("#explore_content_album_of_week_coverart").css("background-image", "url(" + d + ")");
            jQuery("#explore_content_album_of_week_coverart").css("background-size", "cover");
            jQuery("#explore_content_album_of_week_album").text(e);
            jQuery("#explore_content_album_of_week_artist").text(a).attr("href", "/search/" + a);
            this.songs = new SongCollection();
            this.songs.reset(b.get("songs"));
            this.songs.each(this.add)
        }
        return this
    },
    add: function(c) {
        var b = Templates.explore_album_of_the_week_songs;
        var a = new SongView({
            model: c,
            section: this.section,
            template: b,
            tagName: "li",
            className: "explore_content_album_of_week_song explore_songlist_song a_song"
        });
        $("#explore_content_album_of_week_songs").append(a.render().el)
    },
    onPlayAllButtonClicked: function() {
        $(window).trigger({
            type: "lalaNewSongList",
            list: this.songs.toJSON(),
            position: 0,
            section: this.section
        })
    }
});
var LatestLovedView = Backbone.View.extend({
    template: Templates.explore_latest_loved,
    initialize: function() {
        $("#explore").append(this.template());
        _.bindAll(this, "render");
        jQuery("#explore_play_button_latest_loved").bind("click", this.onPlayAllButtonClicked);
        Utils.ShowLoading("#item_rows");
        this.render()
    },
    render: function() {
        var a = new LatestLovedSongsView();
        return this
    }
});
var LatestLovedSongsView = SongListView.extend({
    collectionClass: TimelineLovedCollection,
    section: "explore_latest_songs",
    render: function() {
        return this
    }
});
var LatestPlaylistView = Backbone.View.extend({
    template: Templates.explore_latest_playlists,
    initialize: function() {
        $("#explore").append(this.template());
        _.bindAll(this, "render");
        jQuery("#explore_play_button_latest_loved").bind("click", this.onPlayAllButtonClicked);
        Utils.ShowLoading("#item_rows");
        this.render()
    },
    render: function() {
        var a = new LatestPlaylistSongsView();
        return this
    }
});
var LatestPlaylistSongsView = PlaylistListView.extend({
    collectionClass: LatestPlaylistCollection,
    section: "explore_playlist_songs",
    render: function() {
        return this
    }
});

var ExploreGenres = Backbone.View.extend({
    template: Templates.explore_genres,
    playOnFetch: false,
    initialize: function() {
        this.genres = genre_list;
        $("#explore").append(this.template({
            genres: this.genres
        }));
        $("#right").css("background-color", "#141318");

        this.render()
    },
    render: function() {
        /*var a = this;
         var d = 0;
         var e = 0;
         var f = [];
         for (var b = 0; b < this.genres.length; b++) {
         var c = this.genres[b];
         $genre_el = $("#explore_content_genres").find("a").eq(b);
         if ($genre_el.position().top != d) {
         d = $genre_el.position().top;
         resizeGenreRow(f);
         f = []
         }
         f.push($genre_el)
         }
         resizeGenreRow(f);*/
        return this
    }
    /* add: function (d, b) {
     var a = "#genre_" + b;
     var c = d.topartists.artist.image[2]["#text"].replace("126", "126s");
     $(this.el).find(a).find(".genre_bg").css("background-image", "url(" + c + ")")
     }*/
});

function resizeGenreRow(f) {
    var d = $("#explore_content_genres").width();
    var a = 0;
    var c = f.length;
    for (var b = 0; b < c; b++) {
        a += f[b].width() + 2
    }
    var e = Math.floor((798 - a) / c);
    for (var b = 0; b < c; b++) {
        $row_el = f[b];
        $row_el.css("width", $row_el.width() + e)
    }
    $row_el.css("margin-right", 0)
}
var GenreSongView = SongListView.extend({
    collectionClass: SongGenreCollection,
    section: "explore_genre",
    render: function() {
        $("#explore_nav_genres").addClass("active");
        var a = unescape(this.options.genre);
        $("#explore").append(Templates.feed({
            title: a,
            item_rows_classname: "explore_tags"
        }));
        Utils.ShowLoading("#item_rows");
        return this
    }
});
$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("explore") != -1) {
        var d = c.href.lastIndexOf("explore");
        var b = c.href.substring(d + 8);
        var a = new ExploreView({
            section: b
        })
    } else {
        jQuery("#right").removeClass("top-of-the-week genres album-of-the-week tastemakers top-billboard latest latest-lists")
    }
});
var PlayListDetailsView = Backbone.View.extend({
    el: $("#song_list"),
    template: Templates.playlist_header,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render");
        $("#right").removeAttr("style");
        $("#right").attr("style", "background:#141318");
        Utils.HideSections("#song_list");
        Utils.ShowLoading("#song_list");
        this.model.bind("change", this.render);
        this.model.fetch({
            error: this.error
        })
    },
    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        $("#right").scrollTop(0);
        var a = new PlaylistSongView({
            show_user: false,
            playlist_id: this.playlist_id,
            show_user_in_others: true
        });
        var b = this.model.toJSON();
        jQuery(".song_top_edit_playlist").bind("click", function() {
            EditPlaylist.Show(b)
        });
        var playlist_id = this.playlist_id;
        jQuery(".resort_playlist").bind("click", function() {
            PlaylistResort.Build(playlist_id);
        });
        return this
    }
});
var AlbumDetailsView = Backbone.View.extend({
    el: $("#song_list"),
    template: Templates.album_header,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render");
        $("#right").removeAttr("style");
        $("#right").attr("style", "background:#141318");
        Utils.HideSections("#song_list");
        Utils.ShowLoading("#song_list");
        this.model.bind("change", this.render);
        this.model.fetch({
            error: this.error
        })
    },
    render: function() {
        //console.log(this.model.toJSON());
        $(this.el).html(this.template(this.model.toJSON()));
        $("#right").scrollTop(0);
        var a = new AlbumSongView({
            show_user: false,
            album_id: this.album_id,
            show_user_in_others: true
        });
        try {
            FB.XFBML.parse();
        } catch (a) {
        }
        return this
    }
});
if (typeof (ArtistAllAlbum) == "undefined") {
    ArtistAllAlbum = {}
}
ArtistAllAlbum.Build = function(e) {
    ArtistAllAlbum.Display.request(e)
};
ArtistAllAlbum.Display = {
    method: player_root + "more.php?t=artistallalbum",
    request: function(e) {
        var b = ArtistAllAlbum.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                id: e
            },
            complete: ArtistAllAlbum.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "ArtistAllAlbum.Display.response");
        if (c.success == true) {
            ArtistAllAlbum.BuildList(c.json.albums);
            $(".albums_count").html(c.json.total);
            if (c.json.total < 5)
                $(".show_all_albums").hide();
        }
    }
};
ArtistAllAlbum.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.artist_all_album_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div class="search_song_none_found">У вас нет ни одного альбома</div>'
    }
    $("#artist_albums").html(d)
};
if (typeof (ArtistBio) == "undefined") {
    ArtistBio = {}
}
ArtistBio.Build = function(e) {
    ArtistBio.Display.request(e)
};
ArtistBio.Display = {
    method: player_root + "more.php?t=artistbio",
    request: function(e) {
        var b = ArtistBio.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                id: e
            },
            complete: ArtistBio.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "ArtistBio.Display.response");
        if (c.success == true) {
            ArtistBio.BuildList(c.json.buffer[0].bio);
            //                    alert(c.json.buffer[0].bio);
            //             
            //                   console.log(c);
            //                  console.log(c.json.buffer[0].bio);
            //			ArtistBio.BuildList(c.json.bio);

        }
    }
};
ArtistBio.BuildList = function(f) {

    var d = "";

    var e = Templates.artist_bio;

    //	var b = f[c];	
    d = Templates.artist_bio({
        item: f

    })
    jQuery("#item_rows").html(d)

};
if (typeof (ArtistSimilar) == "undefined") {
    ArtistSimilar = {}
}
ArtistSimilar.Build = function(e) {
    ArtistSimilar.Display.request(e)
};
ArtistSimilar.Display = {
    method: player_root + "more.php?t=artistsimilar",
    request: function(e) {
        var b = ArtistSimilar.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                id: e
            },
            complete: ArtistSimilar.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "ArtistSimilar.Display.response");
        if (c.success == true) {
            ArtistSimilar.BuildList(c.json.buffer);
            $(".artist_count").html(c.json.total);
            if (c.json.total < 5)
                $(".show_all_artists").hide();
        }
    }
};
ArtistSimilar.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.artist_all_artist_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div class="search_song_none_found">У вас нет ни одного исполнителя</div>'
    }
    $("#artist_similar").html(d)
};

if (typeof (ArtistAllVideo) == "undefined") {
    ArtistAllVideo = {}
}
ArtistAllVideo.Build = function(e) {
    ArtistAllVideo.Display.request(e)
};
ArtistAllVideo.Display = {
    method: player_root + "more.php?t=artistallvideo",
    request: function(e) {
        var b = ArtistAllVideo.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                id: e
            },
            complete: ArtistAllVideo.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "ArtistAllVideo.Display.response");
        if (c.success == true) {
            ArtistAllVideo.BuildList(c.json.videos);
            $(".videos_count").html(c.json.total);
            if (c.json.total < 5)
                $(".show_all_videos").hide();
        }
    }
};
ArtistAllVideo.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.artist_all_video_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div class="search_song_none_found">No video found</div>'
    }
    $("#artist_videos").html(d)
};
if (typeof (VideoPlay) == "undefined") {
    VideoPlay = {}
}
VideoPlay.Build = function(e) {
    VideoPlay.Display.request(e)
};
VideoPlay.Display = {
    method: player_root + "more.php?t=video",
    request: function(e) {
        var b = VideoPlay.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                id: e
            },
            complete: VideoPlay.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "VideoPlay.Display.response");
        if (c.success == true) {
            var e = Templates.video_page({
                "video": c.json.video
            });
            Utils.HideSections("#song_list");
            Utils.ShowLoading("#song_list");
            $("#right").css("background-color", "#222222");
            $("#song_list").html(e);
            try {
                FB.XFBML.parse();
            } catch (a) {
            }
        }
    }
};
$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("video/") != -1) {
        var d = c.href.lastIndexOf("video");
        var b = c.href.substring(d + 6);
        var e = new VideoPlay.Build(b);
    }
});
var ComposerDetailsView = Backbone.View.extend({
    el: $("#song_list"),
    template: Templates.composer_header,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render");
        $("#right").removeAttr("style");
        $("#right").attr("style", "background:#141318");
        Utils.HideSections("#song_list");
        Utils.ShowLoading("#song_list");
        this.model.bind("change", this.render);
        this.model.fetch({
            error: this.error
        })
    },
    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        $("#right").scrollTop(0);
        var a = new ComposerSongView({
            show_user: false,
            composer_id: this.composer_id,
            show_user_in_others: true
        });
        ComposerAllAlbum.Build(this.composer_id);
        return this
    }
});
if (typeof (PlaylistResort) == "undefined") {
    PlaylistResort = {}
}
PlaylistResort.Build = function(f) {
    var playlist_id = f;
    jQuery.ajax({
        url: player_root + "more.php?t=playlist&action=resort&id=" + playlist_id,
        type: "POST",
        dataType: "json",
        data: {},
        complete: PlaylistResort.Response
    })
};
PlaylistResort.Reload = function(r) {
    //var b = r.playlist_id;
    var b = PLAYLIST_IN_RESORT;
    var e = new Playlist({
        playlist_id: b
    });
    var a = new PlayListDetailsView({
        model: e,
        playlist_id: b
    });
    $(window).unbind("ReloadPlaylistAfterResort", PlaylistResort.Reload)
    return true
};
PlaylistResort.Response = function(b, c) {
    var SongList = JSON.parse(b.responseText);
    if (SongList != null) {
        var a = SongList.length;
        var d = Templates.resort_item;
        var c = "";
        for (var b = 0; b < a; b++) {
            var e = SongList[b];
            c += d({
                song: e,
                position: b
            })
        }

        jQuery("#resort_playlist_rows").html(c);
        jQuery("#resort_playlist").css({
            "height": "100%"
        });
        var playlist_id = SongList[0].playlist_id;
        PLAYLIST_IN_RESORT = playlist_id;
        $(window).bind("ReloadPlaylistAfterResort", PlaylistResort.Reload);
        jQuery("#resort_playlist_close").bind("click", function() {
            jQuery("#resort_playlist").css({
                "height": "0"
            });
            $(window).trigger({
                type: "ReloadPlaylistAfterResort",
                playlist_id: playlist_id
            });
        });
        jQuery("#resort_playlist_rows").dragsort({
            dragEnd: PlaylistResort.Reorder.dragEnd,
            scrollContainer: "#resort_playlist_rows",
            scrollSpeed: 8,
            dragSelectorExclude: ""
        })
    }
    return true
};
PlaylistResort.Reorder = {
    dragEnd: function() {
        /*var data = $(".resort_playlist_row").map(function () {
         alert($(this).attr("song_id"));
         return $(this).children().html();
         }).get();
         $("input[name=list1SortOrder]").val(data.join("|"));*/
        var songs = "";
        var playlist_id;
        jQuery(".resort_playlist_row").each(function(d, e) {
            songs += ($(this).attr("song_id")) + "==";
            playlist_id = $(this).attr("playlist_id");
        });
        $.ajax({
            type: "POST",
            url: player_root + "more.php?t=playlist&action=doresort&playlist_id=" + playlist_id,
            data: {
                songs: songs
            },
            success: function(response) {
                return false;
            }
        });



        /*var b = false;
         var a = false;
         jQuery(".resort_playlist_row").each(function (d, e) {
         b = !b;
         jQuery(e).removeClass("true false");
         jQuery(e).addClass(b + "");
         var c = parseInt(jQuery(e).attr("position"));
         if (c != d && a == false) {
         a = true;
         
         var b = c.oldPosition;
         var a = c.newPosition;
         var d = AudioPlayer.List.current.splice(b, 1);
         AudioPlayer.List.current.splice(a, 0, d[0]);
         Storage.Set("queue", AudioPlayer.List.current);
         if (AudioPlayer.QueueNumber >= a) {
         AudioPlayer.QueueNumber++;
         Storage.Set("queueNumber", AudioPlayer.QueueNumber)
         }
         if (AudioPlayer.QueueNumber == b) {
         AudioPlayer.QueueNumber = a;
         Storage.Set("queueNumber", AudioPlayer.QueueNumber)
         }
         
         //jQuery(window).trigger({
         //   type: "lalaChangeQueueOrder",
         //    oldPosition: c,
         //    newPosition: d
         //})
         }
         //jQuery(e).attr("position", d);
         //jQuery(e).find(".current_playlist_play_button").attr("id", "current_playlist_play_button_" + d)
         })*/
    }
};
var SongPlaylistCollection = SongCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=playlist&action=songs&id=" + this.playlist_id + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var PlaylistSongView = SongListView.extend({
    collectionClass: SongPlaylistCollection,
    section: "playlist",
    render: function() {
        Utils.ShowLoading("#item_rows");
        return this
    }
});
var SongComposerCollection = SongCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=composer&action=songs&id=" + this.composer_id + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var ComposerSongView = SongListView.extend({
    collectionClass: SongComposerCollection,
    section: "artist",
    render: function() {
        Utils.ShowLoading("#item_rows");
        return this
    }
});
var SongArtistCollection = SongCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=artist&action=songs&id=" + this.artist_id + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var ArtistSongView = SongListView.extend({
    collectionClass: SongArtistCollection,
    section: "artist",
    render: function() {
        // alert("test234656");
        Utils.ShowLoading("#item_rows");
        return this
    }
});
var SongAlbumCollection = SongCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=album&action=songs&id=" + this.album_id + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var AlbumSongView = SongListView.extend({
    collectionClass: SongAlbumCollection,
    section: "artist",
    render: function() {
        Utils.ShowLoading("#item_rows");
        return this
    }
});
var Playlist = Backbone.Model.extend({
    initialize: function() {
        this.set({
            id: this.get("playlist_id")
        })
    },
    urlRoot: player_root + "more.php?t=playlist&action=info&id=",
    url: function() {
        return this.urlRoot + this.get("playlist_id")
    },
    parse: function(a) {
        return a.playlist
    }
});
$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("playlist/") != -1) {
        var d = c.href.lastIndexOf("playlist");
        var b = c.href.substring(d + 9);
        var e = new Playlist({
            playlist_id: b
        });
        var a = new PlayListDetailsView({
            model: e,
            playlist_id: b
        })
    }
});
var Artist = Backbone.Model.extend({
    initialize: function() {
        this.set({
            id: this.get("artist_id")
        })
    },
    urlRoot: player_root + "more.php?t=artist&action=info&id=",
    url: function() {
        return this.urlRoot + this.get("artist_id")
    },
    parse: function(a) {
        return a.artist
    }
});
var Composer = Backbone.Model.extend({
    initialize: function() {
        this.set({
            id: this.get("composer_id")
        })
    },
    urlRoot: player_root + "more.php?t=composer&action=info&id=",
    url: function() {
        return this.urlRoot + this.get("composer_id")
    },
    parse: function(a) {
        return a.composer
    }
});

/*$(window).bind("lalaHistoryChange", function (c) {
 if (c.href.indexOf("artist/") != -1) {
 var d = c.href.lastIndexOf("artist");
 var b = c.href.substring(d + 7);
 var e = new Artist({
 artist_id: b
 });
 var a = new ArtistDetailsView({
 model: e,
 artist_id: b
 });
 
 }
 });*/
if (typeof (ComposerAllAlbum) == "undefined") {
    ComposerAllAlbum = {}
}
ComposerAllAlbum.Build = function(e) {
    ComposerAllAlbum.Display.request(e)
};
ComposerAllAlbum.Display = {
    method: player_root + "more.php?t=composerallalbum",
    request: function(e) {
        var b = ComposerAllAlbum.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                id: e
            },
            complete: ComposerAllAlbum.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "ComposerAllAlbum.Display.response");
        if (c.success == true) {
            ComposerAllAlbum.BuildList(c.json.albums);
        }
    }
};
ComposerAllAlbum.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.artist_all_album_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div class="search_song_none_found">No album found</div>'
    }
    $("#artist_albums").html(d)
};
$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("composer/") != -1) {
        var d = c.href.lastIndexOf("composer");
        var b = c.href.substring(d + 9);
        var e = new Composer({
            composer_id: b
        });
        var a = new ComposerDetailsView({
            model: e,
            composer_id: b
        });
    }
});
var Album = Backbone.Model.extend({
    initialize: function() {
        this.set({
            id: this.get("album_id")
        })
    },
    urlRoot: player_root + "more.php?t=album&action=info&id=",
    url: function() {
        return this.urlRoot + this.get("album_id")
    },
    parse: function(a) {
        return a.album
    }

});
$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("album") != -1) {
        var b = c.href.substring(6);
        if (!isNaN(b)) {
            var e = new Album({
                album_id: b
            });
            var a = new AlbumDetailsView({
                model: e,
                album_id: b
            })
        }
    }
});
var NotificationSongView = Backbone.View.extend({
    timeoutTime: 5000,
    template: Templates.notification_song,
    initialize: function() {
        _.bindAll(this, "render", "cancel", "onNotificationClicked");
        var a = Storage.Get("show_song_desktop_notification");
        if (a == true) {
            this.render()
        }
    },
    render: function() {
        this.cancel();
        try {
            var b = "";
            if (this.model.has("artist")) {
                b = this.model.get("artist");
                if (this.model.has("album")) {
                    b = b + " - " + this.model.get("album")
                }
            }
            window.webkitSongNotification = webkitNotifications.createNotification(this.model.get("image").small, this.model.get("title"), b);
            window.webkitSongNotification.show();
            window.webkitSongNotification.addEventListener("click", this.onNotificationClicked);
            window.webkitSongNotificationTimeout = setTimeout(this.cancel, this.timeoutTime)
        } catch (a) {
        }
    },
    cancel: function() {
        clearTimeout(window.webkitSongNotificationTimeout);
        try {
            window.webkitSongNotification.cancel();
            window.webkitSongNotification.removeEventListener("click", this.onNotificationClicked)
        } catch (a) {
        }
    },
    onNotificationClicked: function() {
        var a = this.model.get("id");
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: "song/" + a
        });
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: "song/" + a
        });
        window.focus()
    }
});
jQuery(window).bind("lalaAudioNewSong", function(b) {
    var c = new Song(b.song);
    var a = new NotificationSongView({
        model: c
    })
});
var NotificationLoveView = Backbone.View.extend({
    timeoutTime: 8000,
    initialize: function() {
        _.bindAll(this, "render", "cancel", "onNotificationClicked");
        var a = Storage.Get("show_love_desktop_notification");
        if (a == true) {
            this.render()
        }
    },
    render: function() {
        this.cancel();
        if (this.model.has("title")) {
            try {
                var c = "";
                if (this.model.get("context") == loggedInUser.username) {
                    c = " from you"
                }
                var a = "";
                if (this.model.has("artist")) {
                    a = " by " + this.model.get("artist")
                }
                window.webkitLoveNotification = webkitNotifications.createNotification(LALA_AVATAR_HOST + "avatar_small_" + this.model.get("username") + ".jpg", this.model.get("username") + " just loved" + c, this.model.get("title") + a);
                window.webkitLoveNotification.show();
                window.webkitLoveNotification.addEventListener("click", this.onNotificationClicked);
                window.webkitLoveNotificationTimeout = setTimeout(this.cancel, this.timeoutTime)
            } catch (b) {
            }
        }
    },
    cancel: function() {
        clearTimeout(window.webkitLoveNotificationTimeout);
        try {
            window.webkitLoveNotification.cancel();
            window.webkitLoveNotification.removeEventListener("click", this.onNotificationClicked)
        } catch (a) {
        }
    },
    onNotificationClicked: function() {
        var a = this.model.get("id");
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: "song/" + a
        });
        window.focus()
    }
});
var NotificationFollowView = Backbone.View.extend({
    timeoutTime: 5000,
    initialize: function() {
        _.bindAll(this, "render", "cancel", "onNotificationClicked");
        var a = Storage.Get("show_follow_desktop_notification");
        if (a == true) {
            this.render()
        }
    },
    render: function() {
        this.cancel();
        try {
            window.webkitFollowNotification = webkitNotifications.createNotification(LALA_AVATAR_HOST + "avatar_small_" + this.model.get("username") + ".jpg", "You Have a New Follower", this.model.get("username") + " just started following you");
            window.webkitFollowNotification.show();
            window.webkitFollowNotification.addEventListener("click", this.onNotificationClicked);
            window.webkitFollowNotificationTimeout = setTimeout(this.cancel, this.timeoutTime)
        } catch (a) {
        }
    },
    cancel: function() {
        clearTimeout(window.webkitFollowNotificationTimeout);
        try {
            window.webkitFollowNotification.cancel();
            window.webkitFollowNotification.removeEventListener("click", this.onNotificationClicked)
        } catch (a) {
        }
    },
    onNotificationClicked: function() {
        var a = this.model.get("username");
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: a
        });
        window.focus()
    }
});
if (typeof (Home) == "undefined") {
    Home = {}
}
var HomeViewHeaderInterval = null;
var HomeView = Backbone.View.extend({
    el: $("#home_section"),
    template: Templates.home,
    events: {
        "submit #home_search_form": "search_click",
        "click #home_search_button": "search_click"
    },
    initialize: function() {
        _.bindAll(this, "search_click", "switchHeader");
        Utils.HideSections("#home_section");
        jQuery("#logo").addClass("selected");
        jQuery("#right").css("background", "#141318");
        this.render()
    },
    render: function() {
        var b = "Bookmarklet";
        if (navigator.userAgent.toLowerCase().indexOf("mozilla") != -1) {
            b = "Firefox"
        }
        if (navigator.userAgent.toLowerCase().indexOf("safari") != -1) {
            b = "Safari"
        }
        if (navigator.userAgent.toLowerCase().indexOf("chrome") != -1) {
            b = "Chrome"
        }
        $(this.el).html(this.template({
            browser: b,
            loggedInUser: loggedInUser
        }));
        var a = Templates.home_footer;
        $(this.el).find("#home_footer_container").append(a({
            year: new Date().getFullYear()
        }));
        setTimeout(this.addSocialButtons, 700);
        HomeViewHeaderInterval = setInterval(this.switchHeader, 3500);
        return this
    },
    addSocialButtons: function() {
        jQuery("#twitter_js_social_button").remove();
        var a = document.createElement("script");
        a.setAttribute("id", "twitter_js_social_button");
        a.setAttribute("src", "http://platform.twitter.com/widgets.js");
        document.body.appendChild(a);
        jQuery("#facebook-jssdk").remove();
        var b = document.createElement("script");
        b.setAttribute("id", "facebook-jssdk");
        b.setAttribute("src", "//connect.facebook.net/en_US/all.js#xfbml=1");
        document.body.appendChild(b);
        jQuery("#google_js_social_button").remove();
        var c = document.createElement("script");
        c.setAttribute("id", "google_js_social_button");
        c.setAttribute("src", "https://apis.google.com/js/plusone.js");
        document.body.appendChild(c);
        jQuery("#home_share").removeClass("hidden")
    },
    search_click: function() {
        var a = jQuery("#home_search").attr("value");
        if (a != "") {
            jQuery(window).trigger({
                type: "lalaNeedHistoryChange",
                href: "search/" + a.replace(/ /g, "+")
            })
        }
        return false
    },
    header: 1,
    headerTimeout: null,
    switchHeader: function() {
        clearTimeout(this.headerTimeout);
        var a = $(".home_header").length;
        $(".home_header_on").removeClass("home_header_on").addClass("home_header_out");
        $(".home_header").eq(this.header % a).addClass("home_header_on");
        this.headerTimeout = setTimeout(function() {
            $(".home_header_out").removeClass("home_header_out")
        }, 650);
        this.header++
    }



});


jQuery(window).bind("lalaHistoryChange", function(b) {
    clearInterval(HomeViewHeaderInterval);
    if (b.href == "" || b.href == "/") {
        var a = new HomeView();
        $("#right").unbind("scrollBottom", this.onScrollBottom);
        $('#home_search').bind('keypress', function(e) {
            var a = $('#home_search').val();
            if (a.length > 0) {
                HomeSearchSuggest(a)
            } else {
                $("#search_suggess").addClass("display_none");
            }
        })
    } else {
        jQuery("#logo").removeClass("selected")
    }
});

function HomeSearchSuggest(e) {
    $.ajax({
        url: player_root + "more.php?t=suggess",
        type: "GET",
        data: {
            query: e
        },
        dataType: "json",
        beforeSend: function() {
            $("#loading").show()
        },
        success: function(t) {
            if (t.status_code == 200) {
                var n = t.suggess;
                if (n != 'null') {
                    ThisArrayLen = n.length;
                    var r = "";
                    for (var i = 0; i < ThisArrayLen; i++)
                        t.suggess[i].title != null && (r += '<li><a href="/song/' + t.suggess[i].id + '">' + t.suggess[i].title + '</a></li>');
                    $("#search_suggess").removeClass("display_none");
                    $("#search_suggess").html(r);
                }
                jQuery(".suggess_items").bind("click", function() {
                    var f = jQuery(this).attr("data-song-id");
                    alert(f);
                });
            }
        }
    })
}

var SongSearchView = SongListView.extend({
    collectionClass: SongSearchCollection,
    section: "search",
    query: "",
    render: function() {
        $("#right").removeAttr("style");
        $("#right").attr("class", "search-results");
        _.bindAll(this, "onInitialSearchFetch", "noResults");
        this.query = unescape(this.options.q);
        this.songs.bind("reset", this.onInitialSearchFetch);
        $(this.el).html(Templates.search_song_results({
            title: this.query.replace(/\+/g, " "),
            item_rows_classname: "search_songs"
        }));
        return this
    },
    onInitialSearchFetch: function() {
        if (this.songs.totalResults > 0) {
            $("#search_results_total").html('<em id="search_results_num">' + this.songs.totalResults + "</em> songs for keyword")
        } else {
            $("#item_rows").html('<div class="search_song_none_found">No songs found. Please try another search term.</div>');
            //this.noResults()
        }
    },
    noResults: function() {
        $(this.el).html(Templates.search_song_none({
            title: this.query.replace(/\+/g, " ")
        }))
    }
});
if (typeof (SearchAlbum) == "undefined") {
    SearchAlbum = {}
}
SearchAlbum.Build = function(e) {
    SearchAlbum.Display.request(e)
};
SearchAlbum.Display = {
    method: player_root + "more.php?t=searchalbum",
    request: function(e) {
        var b = SearchAlbum.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                q: e
            },
            complete: SearchAlbum.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "SearchAlbum.Display.response");
        if (c.success == true) {
            SearchAlbum.BuildList(c.json.albums);
        }
    }
};
SearchAlbum.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.artist_all_album_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div class="search_song_none_found">No album found</div>'
    }
    $("#search_albums").html(d)
};
if (typeof (SearchArtist) == "undefined") {
    SearchArtist = {}
}
SearchArtist.Build = function(e) {
    SearchArtist.Display.request(e)
};
SearchArtist.Display = {
    method: player_root + "more.php?t=searchartist",
    request: function(e) {
        var b = SearchArtist.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                q: e
            },
            complete: SearchArtist.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "SearchArtist.Display.response");
        if (c.success == true) {
            SearchArtist.BuildList(c.json.albums);
        }
    }
};
SearchArtist.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.search_artist_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div class="search_song_none_found">No artists found</div>'
    }
    $("#search_artists").html(d)
};
if (typeof (SearchComposer) == "undefined") {
    SearchComposer = {}
}
SearchComposer.Build = function(e) {
    SearchComposer.Display.request(e)
};
SearchComposer.Display = {
    method: player_root + "more.php?t=searchcomposer",
    request: function(e) {
        var b = SearchComposer.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                q: e
            },
            complete: SearchComposer.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "SearchComposer.Display.response");
        if (c.success == true) {
            SearchComposer.BuildList(c.json.composers);
        }
    }
};
SearchComposer.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.search_composer_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div class="search_song_none_found">No composers found</div>'
    }
    $("#search_composers").html(d)
};
jQuery(window).bind("lalaHistoryChange", function(b) {
    if (b.href.indexOf("search/") != -1) {
        var a = b.href.substr(b.href.lastIndexOf("/") + 1);
        Utils.HideSections("#song_list");
        var c = new SongSearchView({
            q: a,
            show_user: false,
            show_user_in_others: true
        });
        SearchAlbum.Build(a);
        SearchArtist.Build(a);
        SearchComposer.Build(a);
        Utils.ShowLoading("#item_rows")
    }
});
jQuery("#top_search_form").bind("submit", function() {
    var a = jQuery("#top_search_input").attr("value");
    if (a != "") {
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: "search/" + a.replace(/ /g, "+")
        })
    }
    return false
});
var TutorialView = Backbone.View.extend({
    el: $("#tutorial_container"),
    template: Templates.tutorial,
    events: {
        "click #tutorial_close": "onCloseClicked"
    },
    titles: ["Your Profile", "See What's Trending", "Explore Nota", "Get Social", "Make Nota Yours", "Turn Nota Up", "Thanks again and enjoy Nota!"],
    images: ["tutorial-profile.png", "tutorial-trending.png", "tutorial-explore.png", "tutorial-social.png", "tutorial-customize.png", "tutorial-extensions.png"],
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render", "onCloseClicked");
        this.number = parseInt(this.number);
        this.render()
    },
    render: function() {
        $("#full_cover").removeClass("display_none");
        if ($("#tutorial_header").length <= 0) {
            $(this.el).removeClass("display_none welcome").html(this.template({
                pages: this.titles
            }))
        }
        $nextbutton = $("#tutorial_bottom_next");
        $prevbutton = $("#tutorial_bottom_prev");
        if (Templates["tutorial_" + Number(this.number + 1)]) {
            $nextbutton.removeClass("display_none").attr("href", "/tutorial/" + (this.number + 1))
        } else {
            $nextbutton.addClass("display_none")
        }
        if (this.number - 1 > 0) {
            $prevbutton.removeClass("display_none").attr("href", "/tutorial/" + (this.number - 1))
        } else {
            $prevbutton.addClass("display_none")
        }
        $("#tutorial_carousel a.active").removeClass("active");
        $("#tutorial_carousel a").eq(this.number - 1).addClass("active");
        var f = Templates["tutorial_" + this.number];
        var c = this.images[this.number - 1];
        var e = this.titles[this.number - 1];
        var b = $(".tutorial_content").length;
        $("#tutorial_middle").append('<div class="tutorial_content" style="top:' + b * 375 + 'px">' + f({
            image: c,
            title: e
        }) + "</div>");
        if (b > 0) {
            $(".tutorial_content").eq(0).css("top", -375);
            var a = setTimeout(function() {
                $(".tutorial_content").eq(1).css("top", 0)
            }, 10);
            var d = setTimeout(function() {
                $(".tutorial_content").eq(0).remove()
            }, 250)
        }
        return this
    },
    onCloseClicked: function() {
        $("#tutorial_header").remove();
        $("#tutorial_container").addClass("display_none");
        $("#full_cover").addClass("display_none");
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: ""
        })
    }
});
var WelcomeToTheNewView = Backbone.View.extend({
    el: $("#tutorial_container"),
    template: Templates.welcome_to_the_new,
    events: {
        "click #tutorial_close": "onCloseClicked",
        "click .tutorial_in_link": "onInLinkClicked"
    },
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render", "onCloseClicked");
        this.render()
    },
    render: function() {
        $("#full_cover").removeClass("display_none");
        $(this.el).removeClass("display_none").addClass("welcome").html(this.template());
        $nextbutton = $("#tutorial_bottom_next");
        return this
    },
    onCloseClicked: function() {
        $("#tutorial_container").addClass("display_none");
        $("#full_cover").addClass("display_none");
        jQuery(window).trigger({
            type: "lalaNeedHistoryChange",
            href: TutorialLastHistory
        })
    },
    onInLinkClicked: function() {
        $("#tutorial_container").addClass("display_none");
        $("#full_cover").addClass("display_none")
    }
});
$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("tutorial") != -1) {
        var f = c.href.lastIndexOf("tutorial");
        var a = c.href.substring(f + 9);
        if (a == "") {
            a = 1
        }
        var d = new TutorialView({
            number: a
        })
    } else {
        if (c.href.indexOf("welcome-to-the-new-lala") != -1) {
            var b = new WelcomeToTheNewView()
        } else {
            TutorialLastHistory = c.href
        }
    }
});
TutorialLastHistory = "";
if (typeof (TopTips) == "undefined") {
    TopTips = {}
}
TopTips.Interval = 30000;
TopTips.Position = 0;
TopTips.Tips = ['<strong>Подсказка:</strong> Настройте <a id="top_tip_link" href="/settings/design">тему профиля</a> под себя', '<strong>Подсказка:</strong> Войдите с помощью вашего аккаунта в <a id="top_tip_link" href="/settings/social">социальной сети </a>', '<strong>Подсказка:</strong> Посмотрите <a id="top_tip_link" href="/explore/top-of-the-week">лучшие песни за неделю </a>', '<strong>Подсказка:</strong> Посмотрите <a id="top_tip_link" href="/explore/latest">последние избранное песни за неделю </a>'];
TopTips.Next = function() {
    if (TopTips.Position < TopTips.Tips.length - 1) {
        TopTips.Position++
    } else {
        TopTips.Position = 0
    }
    TopTips.Show()
};
TopTips.Show = function() {
    $top_tip = $("#top_tip");
    $top_tip.addClass("top_tip_hidden");
    var a = TopTips.Tips[TopTips.Position];
    if (a.indexOf("Install our extensio") != -1) {
        var b = "chrome";
        if (navigator.userAgent.toLowerCase().indexOf("mozilla") != -1) {
            b = "firefox"
        }
        if (navigator.userAgent.toLowerCase().indexOf("safari") != -1) {
            b = "safari"
        }
        if (navigator.userAgent.toLowerCase().indexOf("chrome") != -1) {
            b = "chrome"
        }
        a = a.replace(/chrome/g, b)
    }
    $top_tip.html(a);
    $top_tip.css("width", $top_tip.outerWidth());
    setTimeout(function() {
        $top_tip.removeClass("top_tip_hidden")
    }, "1000")
};
TopTips.Click = function() {
    var a = jQuery(this).attr("href");
    jQuery(window).trigger({
        type: "lalaTipClick",
        tip_type: "top",
        href: a
    });
    setTimeout(TopTips.Next, 5000)
};
jQuery(document).ready(function() {
    TopTips.Tips = Utils.Shuffle(TopTips.Tips);
    setTimeout(TopTips.Show, 1500);
    setInterval(TopTips.Next, TopTips.Interval)
});
jQuery("#top_tip_link").live("click", TopTips.Click);
var ErrorPageView = Backbone.View.extend({
    events: {
        "submit #home_search_form": "search_click",
        "click #home_search_button": "search_click"
    },
    template: Templates.error,
    initialize: function() {
        _.bindAll(this, "render");
        this.render()
    },
    render: function() {
        $("#right").css("background", "#141318");
        $(this.el).html(this.template())
    },
    search_click: function() {
        var a = jQuery("#home_search").attr("value");
        if (a != "") {
            jQuery(window).trigger({
                type: "lalaNeedHistoryChange",
                href: "search/" + a
            })
        }
        return false
    }
});
jQuery(window).bind("lalaShowErrorPage", function(b) {
    var a = $(b.el);
    var c = new ErrorPageView({
        el: a
    })
});




//#################################################################################################################################################################################################################################

if (typeof (UserList) == "undefined") {
    UserList = {}
}
UserList.Build = function() {
    Utils.HideSections("#settings");
    $("#right").css("background", "#141318");
    var userlist_tempale = Templates.userlist_head();
    jQuery(".settings_tab").removeClass("selected");
    jQuery("#settings").html(userlist_tempale);
    var a = Templates.settings_friends();
    jQuery("#settings_middle").html(a);
    jQuery("#settings_bottom").html("");
    jQuery("#find_friends_form").bind("submit", UserList.Search.submit);
    UserList.MaybeFriends.request()
};
UserList.Search = {
    method: player_root + "more.php?t=settings&action=search&q=",
    submit: function() {
        var a = jQuery("#find_friends_input").attr("value");
        if (a != "") {
            UserList.Search.request(a)
        }
        return false
    },
    request: function(b) {
        Utils.ShowLoading("#find_friends_results");
        var a = Utils.get_cookie("_xsrf");
        jQuery.ajax({
            url: UserList.Search.method + b,
            type: "GET",
            dataType: "json",
            data: {
                _xsrf: a
            },
            complete: UserList.Search.response,
            cache: false
        })
    },
    response: function(a, c) {
        jQuery("#find_friends_results").empty();
        jQuery("#settings_friends_header").text("Search Results");
        var b = Utils.APIResponse(a, "UserList.Search.response", "There was a problem. Please try again.");
        if (b.success == true) {
            UserList.BuildFriends(b.json.users)
        }
    }
};
UserList.MaybeFriends = {
    method: player_root + "more.php?t=userlist",
    request: function(a) {
        Utils.ShowLoading("#find_friends_results");
        var b = UserList.MaybeFriends.method.replace("%user%", "lechchut");
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            complete: UserList.MaybeFriends.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "UserList.MaybeFriends.response");
        if (c.success == true) {
            var a = c.json.users.length;
            jQuery("#find_friends_results").empty();
            //jQuery("#settings_friends_header").text("Friend suggestions");
            UserList.BuildFriends(c.json.users);
        }
    }
};
UserList.BuildFriends = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.common_users;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                user: b,
                position: c
            })
        }
    } else {
        d = '<div id="find_friends_none">No users found</div>'
    }
    jQuery("#find_friends_results").html(d)
};

$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("userlist") != -1) {
        UserList.Build();
        /*var d = c.href.lastIndexOf("playlist");
         var b = c.href.substring(d + 9);
         var e = new Playlist({
         playlist_id: b
         });
         var a = new PlayListDetailsView({
         model: e,
         playlist_id: b
         })*/
    }
});



//#################################################################################################################################################################################################################################
if (typeof (AlbumList) == "undefined") {
    AlbumList = {}
}
AlbumList.Build = function(e) {
    Utils.HideSections("#song_list");
    $("#right").css("background", "#141318");
    var b = Templates.albums_head();
    jQuery("#song_list").html(b);
    AlbumList.Display.request(e)
};
AlbumList.Display = {
    method: player_root + "more.php?t=albumlist",
    request: function(e) {
        var b = AlbumList.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                letter: e
            },
            complete: AlbumList.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "AlbumList.Display.response");
        if (c.success == true) {
            AlbumList.BuildList(c.json.albums);
        }
    }
};
AlbumList.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.album_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div id="error_message">No albums found.</div>'
    }
    jQuery("#site_rows").html(d)
};
AlbumList.Search = {
    method: player_root + "more.php?t=settings&action=search&q=",
    submit: function() {
        var a = jQuery("#find_friends_input").attr("value");
        if (a != "") {
            AlbumList.Search.request(a)
        }
        return false
    },
    request: function(b) {
        Utils.ShowLoading("#find_friends_results");
        var a = Utils.get_cookie("_xsrf");
        jQuery.ajax({
            url: AlbumList.Search.method + b,
            type: "GET",
            dataType: "json",
            data: {
                _xsrf: a
            },
            complete: AlbumList.Search.response,
            cache: false
        })
    },
    response: function(a, c) {
        jQuery("#find_friends_results").empty();
        jQuery("#settings_friends_header").text("Search Results");
        var b = Utils.APIResponse(a, "AlbumList.Search.response", "There was a problem. Please try again.");
        if (b.success == true) {
            AlbumList.BuildFriends(b.json.users)
        }
    }
};



if (typeof (ArtistList) == "undefined") {
    ArtistList = {}
}
ArtistList.Build = function(e) {
    Utils.HideSections("#song_list");
    $("#right").css("background", "#141318");
    var b = Templates.artists_head();
    jQuery("#song_list").html(b);
    ArtistList.Display.request(e)
};
ArtistList.Display = {
    method: player_root + "more.php?t=artistlist",
    request: function(e) {

        var b = ArtistList.Display.method;
        jQuery.ajax({
            url: b,
            type: "GET",
            dataType: "json",
            data: {
                letter: e
            },
            complete: ArtistList.Display.response,
            cache: false
        })
    },
    response: function(b, d) {
        var c = Utils.APIResponse(b, "ArtistList.Display.response");
        if (c.success == true) {
            ArtistList.BuildList(c.json.artists);

            $('#site_rows').scrollPagination({
                'contentPage': '/more.php?t=artistlist', // the url you are fetching the results
                'contentData': {}, // these are the variables you can pass to the request, for example: children().size() to know which page you are
                'scrollTarget': $('#sites_list'), // who gonna scroll? in this example, the full window
                'heightOffset': 10, // it gonna request when scroll is 10 pixels before the page ends
                'beforeLoad': function() { // before load function, you can display a preloader div
                    $('#loading').fadeIn();
                },
                'afterLoad': function(elementsLoaded) { // after loading content, you can use this function to animate your new elements
                    // Bo loading o day
                    //alert(JSON.stringify(LOAD_MORE_ARRAY));
                    ArtistList.BuildList(LOAD_MORE_ARRAY.artists);
                    var i = 0;
                    //$(elementsLoaded).fadeInWithDelay();
                    /*if ($('#song_list').children().size() > 100){ // if more than 100 results already loaded, then stop pagination (only for testing)
                     //hoen khong tim thay gi
                     jQuery("#site_rows").html('<div id="error_message">No artist found.</div>')
                     $('#song_list').stopScrollPagination();
                     }*/
                }
            });


        }
    }
};
ArtistList.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.artist_item;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b,
                position: c
            })
        }
    } else {
        d = '<div id="error_message">No artist found.</div>'
    }
    jQuery("#site_rows").append(d)
};
ArtistList.Search = {
    method: player_root + "more.php?t=settings&action=search&q=",
    submit: function() {
        var a = jQuery("#find_friends_input").attr("value");
        if (a != "") {
            ArtistList.Search.request(a)
        }
        return false
    },
    request: function(b) {
        Utils.ShowLoading("#find_friends_results");
        var a = Utils.get_cookie("_xsrf");
        jQuery.ajax({
            url: ArtistList.Search.method + b,
            type: "GET",
            dataType: "json",
            data: {
                _xsrf: a
            },
            complete: ArtistList.Search.response,
            cache: false
        })
    },
    response: function(a, c) {
        jQuery("#find_friends_results").empty();
        jQuery("#settings_friends_header").text("Search Results");
        var b = Utils.APIResponse(a, "ArtistList.Search.response", "There was a problem. Please try again.");
        if (b.success == true) {
            ArtistList.BuildFriends(b.json.users)
        }
    }
};



$(".show_all_albums").live("click", function() {

    $("#artist_albums_content").css("height", "auto");

    $(".show_all_albums").hide();

});
$(".show_all_artists").live("click", function() {

    $("#artist_similar_content").css("height", "auto");

    $(".show_all_artists").hide();

});
$(".show_all_videos").live("click", function() {

    $("#artist_videos_content").css("height", "auto");

    $(".show_all_videos").hide();

})




var ArtistDetailsView = Backbone.View.extend({
    el: $("#song_list"),
    template: Templates.artist_header,
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "render");
        $("#right").removeAttr("style");
        $("#right").attr("style", "background:#141318");
        Utils.HideSections("#song_list");
        Utils.ShowLoading("#song_list");
        this.model.bind("change", this.render);
        this.model.fetch({
            error: this.error
        })
    },
    render: function() {
        $(this.el).html(this.template(this.model.toJSON()));
        $("#right").scrollTop(0);
        var artist_id_tab = this.artist_id;
        jQuery("#load_artist_song_tab").click(function() {
            jQuery("#artist_albums_content").addClass("display_none");
            jQuery("#artist_similar_content").addClass("display_none");
            jQuery("#item_rows").removeClass("display_none");
            jQuery(".song_tab").removeClass("selected");
            jQuery("#load_artist_song_tab").addClass("selected");

            var f = new ArtistSongsView({
                model: Song,
                artist_id: artist_id_tab,
                section: artist_id_tab,
                el: $("item_rows"),
                show_user: false
            })
        });
        jQuery("#load_artist_bio_tab").click(function() {
            jQuery("#artist_albums_content").addClass("display_none");
            jQuery("#artist_similar_content").addClass("display_none");
            jQuery("#item_rows").removeClass("display_none");
            ArtistBio.Build(artist_id_tab);
            jQuery(".song_tab").removeClass("selected");
            jQuery("#load_artist_bio_tab").addClass("selected");

        })
        jQuery("#load_artist_album_tab").click(function() {
            jQuery("#artist_albums_content").removeClass("display_none");
            jQuery("#artist_similar_content").addClass("display_none");
            jQuery("#item_rows").addClass("display_none");
            ArtistAllAlbum.Build(artist_id_tab);
            jQuery(".song_tab").removeClass("selected");
            jQuery("#load_artist_album_tab").addClass("selected");
        })
        jQuery("#load_artist_similar_tab").click(function() {
            jQuery("#artist_albums_content").addClass("display_none");
            jQuery("#artist_similar_content").removeClass("display_none");
            jQuery("#item_rows").addClass("display_none");
            ArtistSimilar.Build(artist_id_tab);
            jQuery(".song_tab").removeClass("selected");
            jQuery("#load_artist_similar_tab").addClass("selected");
        })

        jQuery(".song_tab").removeClass("selected");
        jQuery("#load_artist_song_tab").addClass("selected");
        return this
    }
});
var ArtistSongsCollection = SongCollection.extend({
    artist_id: null,
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=artist&action=songs&id=" + this.artist_id + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.songs
    }
});
var ArtistSongsView = SongListView.extend({
    collectionClass: ArtistSongsCollection,
    section: "user",
    render: function() {
        // alert("test");
        Utils.ShowLoading("#item_rows");
        jQuery("#item_rows").removeClass("feed");
        return this
    }
});


$(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("artist/") != -1) {
        var d = c.href.lastIndexOf("artist");
        var b = c.href.substring(d + 7);
        var loc = window.location.href,
                index = loc.indexOf('?');

        if (index > 0) {
            window.location = loc.substring(0, index);
        }
        var e = new Artist({
            artist_id: b
        });
        var a = new ArtistDetailsView({
            model: e,
            artist_id: b
        });

    }
});

if (typeof (ChangeLanguage) == "undefined") {
    ChangeLanguage = {}
}
ChangeLanguage.Build = function(e) {
    //        var b = ChangeLanguage.Build.method;
    jQuery.ajax({
        url: "/lang.php?",
        type: "GET",
        dataType: "json",
        data: {
            get: e
        },
        complete: function(b, d) {
            var c = JSON.parse(b.responseText);
            LANG_ARRAY = c;
            $.each(c, function(key, value) {
                $('[data-translate-text="' + key + '"]').html(value);
                //					console.log(key+ ' --> tranlated to --> ' + value);
            });
        },
        cache: false
    })
};

  //console.log('lang end');
jQuery(window).bind("lalaHistoryChange", function(b) {

  //console.log(LANG_ARRAY);
    if (LANG_ARRAY != null) {
        $.each(LANG_ARRAY, function(key, value) {
            $('[data-translate-text="' + key + '"]').html(value);
            //console.log(key+ ' --> tranlated to --> ' + value);
        });
    } else {
        ChangeLanguage.Build(default_lang);

    }
});












var SiteModel = Backbone.Model.extend({
    //Should be nothing here
});
var Site = Backbone.View.extend({
    el: $("#song_list"),
    collectionClass: null,
    show_user: true,
    show_user_in_others: false,
    itemRows: null,
    scrollDiv: $("#right"),
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch");
        $("#right").unbind();
        this.songs = new this.collectionClass(a);
        this.songs.bind("add", this.onAdd);
        this.songs.bind("reset", this.onInitialFetch);
        this.songs.bind("fetch", this.onFetch);
        this.render();
        this.itemRows = $("#site_rows");
        this.scrollDiv = $("#right");
        $(this.scrollDiv).unbind();
        this.songs.fetch();
    },
    scrollFeed: function(c) {
        var a = $(".song_tabs");
        if (a.length > 0) {
            if (this.fromtop == undefined) {
                this.fromtop = a.offset().top
            }
            var b = $(c.target);
            var d = b.scrollTop();
            if (d + 44 >= this.fromtop) {
                a.addClass("fixed")
            } else {
                if (a.hasClass("fixed")) {
                    a.removeClass("fixed")
                }
            }
        }
    },
    onInitialFetch: function(a) {
        $(this.scrollDiv).scrollTop(0);
        $(this.itemRows).empty();
        this.songs.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        var c = (JSON.stringify(b));
        c = JSON.parse(c)
        var e = Templates.artist_item({
            item: c
        });
        $(this.itemRows).append(e)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        $(this.scrollDiv).unbind();
        jQuery("#load_more").remove();
        if (this.songs.hasMore == true) {
            var a = Templates.list_load_more();
            $(this.itemRows).append(a);
            $(this.scrollDiv).bind("scroll", Utils.ScrollBottom);
            $(this.scrollDiv).bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $(this.scrollDiv).unbind("scrollBottom", this.onScrollBottom);
        if (this.songs.hasMore) {
            this.songs.start += this.songs.results;
            this.songs.fetch({
                add: true
            })
        }
    }
});
var SitePlaylist = Backbone.View.extend({
    el: $("#song_list"),
    collectionClass: null,
    show_user: true,
    show_user_in_others: false,
    itemRows: null,
    scrollDiv: $("#right"),
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch");
        $("#right").unbind();
        this.songs = new this.collectionClass(a);
        this.songs.bind("add", this.onAdd);
        this.songs.bind("reset", this.onInitialFetch);
        this.songs.bind("fetch", this.onFetch);
        this.render();
        this.itemRows = $("#site_rows");
        this.scrollDiv = $("#right");
        $(this.scrollDiv).unbind();
        this.songs.fetch();
    },
    scrollFeed: function(c) {
        var a = $(".song_tabs");
        if (a.length > 0) {
            if (this.fromtop == undefined) {
                this.fromtop = a.offset().top
            }
            var b = $(c.target);
            var d = b.scrollTop();
            if (d + 44 >= this.fromtop) {
                a.addClass("fixed")
            } else {
                if (a.hasClass("fixed")) {
                    a.removeClass("fixed")
                }
            }
        }
    },
    onInitialFetch: function(a) {
        $(this.scrollDiv).scrollTop(0);
        $(this.itemRows).empty();
        this.songs.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        var c = (JSON.stringify(b));
        c = JSON.parse(c)
        var e = Templates.playlist_item({
            item: c
        });
        $(this.itemRows).append(e)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        $(this.scrollDiv).unbind();
        jQuery("#load_more").remove();
        if (this.songs.hasMore == true) {
            var a = Templates.list_load_more();
            $(this.itemRows).append(a);
            $(this.scrollDiv).bind("scroll", Utils.ScrollBottom);
            $(this.scrollDiv).bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $(this.scrollDiv).unbind("scrollBottom", this.onScrollBottom);
        if (this.songs.hasMore) {
            this.songs.start += this.songs.results;
            this.songs.fetch({
                add: true
            })
        }
    }
});
var SiteAlbum = Backbone.View.extend({
    el: $("#song_list"),
    collectionClass: null,
    show_user: true,
    show_user_in_others: false,
    itemRows: null,
    scrollDiv: $("#right"),
    initialize: function(a) {
        _.extend(this, a);
        _.bindAll(this, "onInitialFetch", "onAdd", "onScrollBottom", "add", "onFetch");
        $("#right").unbind();
        this.songs = new this.collectionClass(a);
        this.songs.bind("add", this.onAdd);
        this.songs.bind("reset", this.onInitialFetch);
        this.songs.bind("fetch", this.onFetch);
        this.render();
        this.itemRows = $("#site_rows");
        this.scrollDiv = $("#right");
        $(this.scrollDiv).unbind();
        this.songs.fetch();
    },
    scrollFeed: function(c) {
        var a = $(".song_tabs");
        if (a.length > 0) {
            if (this.fromtop == undefined) {
                this.fromtop = a.offset().top
            }
            var b = $(c.target);
            var d = b.scrollTop();
            if (d + 44 >= this.fromtop) {
                a.addClass("fixed")
            } else {
                if (a.hasClass("fixed")) {
                    a.removeClass("fixed")
                }
            }
        }
    },
    onInitialFetch: function(a) {
        $(this.scrollDiv).scrollTop(0);
        $(this.itemRows).empty();
        this.songs.each(this.add);
        this.showLoadingMore()
    },
    onAdd: function(a) {
        this.add(a)
    },
    add: function(b) {
        var c = (JSON.stringify(b));
        c = JSON.parse(c)
        var e = Templates.album_item({
            item: c
        });
        $(this.itemRows).append(e)
    },
    onFetch: function(a) {
        this.showLoadingMore()
    },
    showLoadingMore: function() {
        $(this.scrollDiv).unbind();
        jQuery("#load_more").remove();
        if (this.songs.hasMore == true) {
            var a = Templates.list_load_more();
            $(this.itemRows).append(a);
            $(this.scrollDiv).bind("scroll", Utils.ScrollBottom);
            $(this.scrollDiv).bind("scrollBottom", this.onScrollBottom)
        }
    },
    onScrollBottom: function(a) {
        $(this.scrollDiv).unbind("scrollBottom", this.onScrollBottom);
        if (this.songs.hasMore) {
            this.songs.start += this.songs.results;
            this.songs.fetch({
                add: true
            })
        }
    }
});
var SiteCollection = Backbone.Collection.extend({
    start: 0,
    letter: null,
    results: 20,
    model: SiteModel,
    initialize: function(a) {
        _.extend(this, a)
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.items
    }
});
var ArtistsCollection = SiteCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=artistlist&letter=" + this.letter + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.artists
    }
});
var PlaylistCollection = SiteCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=playlistlist&letter=" + this.letter + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.playlists
    }
});
var AlbumsCollection = SiteCollection.extend({
    hasMore: true,
    url: function() {
        return player_root + "more.php?t=albumlist&letter=" + this.letter + "&start=" + this.start + "&results=" + this.results
    },
    parse: function(a) {
        this.hasMore = ((a.total - a.start) >= this.results);
        return a.albums
    }
});
var ArtistsView = Site.extend({
    collectionClass: ArtistsCollection,
    section: "user",
    render: function() {
        Utils.ShowLoading("#site_rows");
        jQuery("#site_rows").removeClass("feed");
        return this
    }
});
var PlaylistView = SitePlaylist.extend({
    collectionClass: PlaylistCollection,
    section: "user",
    render: function() {
        Utils.ShowLoading("#site_rows");
        jQuery("#site_rows").removeClass("feed");
        return this
    }
});
var AlbumsView = SiteAlbum.extend({
    collectionClass: AlbumsCollection,
    section: "user",
    render: function() {
        Utils.ShowLoading("#site_rows");
        jQuery("#site_rows").removeClass("feed");
        return this
    }
});

jQuery(window).bind("lalaHistoryChange", function(b) {
    if (b.href == "artists") {
        Utils.HideSections("#song_list");
        $("#right").css("background", "#141318");
        var b = Templates.artists_head();
        jQuery("#song_list").html(b);

        var f = new ArtistsView({
            model: SiteModel,
            letter: "",
            el: $("site_rows")
        })
    }
});

jQuery(window).bind("lalaHistoryChange", function(b) {
    if (b.href == "playlist") {
        Utils.HideSections("#song_list");
        $("#right").css("background", "#141318");
        var b = Templates.playlist_head();
        jQuery("#song_list").html(b);

        var f = new PlaylistView({
            model: SiteModel,
            letter: "",
            el: $("site_rows")
        })
    }
});

jQuery(window).bind("lalaHistoryChange", function(b) {
    if (b.href == "albums") {
        Utils.HideSections("#song_list");
        $("#right").css("background", "#141318");
        var b = Templates.albums_head();
        jQuery("#song_list").html(b);
        var f = new AlbumsView({
            model: SiteModel,
            letter: "",
            el: $("site_rows")
        })
    }
});
jQuery(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("albums/") != -1) {
        Utils.HideSections("#song_list");
        $("#right").css("background", "#141318");
        var b = Templates.albums_head();
        jQuery("#song_list").html(b);
        var d = c.href.lastIndexOf("artist");
        var b = c.href.substring(d + 8);
        var j = new AlbumsView({
            model: SiteModel,
            letter: b,
            el: $("site_rows")
        })
    }
});
jQuery(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("artists/") != -1) {
        Utils.HideSections("#song_list");
        $("#right").css("background", "#141318");
        var b = Templates.artists_head();
        jQuery("#song_list").html(b);
        var d = c.href.lastIndexOf("artists");
        var b = c.href.substring(d + 8);
        var j = new ArtistsView({
            model: SiteModel,
            letter: b,
            el: $("site_rows")
        })
    }
});
jQuery(window).bind("lalaHistoryChange", function(c) {
    if (c.href.indexOf("playlist/") != -1) {
        Utils.HideSections("#song_list");
        $("#right").css("background", "#141318");
        var b = Templates.playlist_head();
        jQuery("#song_list").html(b);
        var d = c.href.lastIndexOf("playlist");
        var b = c.href.substring(d + 9);
        var j = new PlaylistView({
            model: SiteModel,
            letter: b,
            el: $("site_rows")
        })
    }
});
jQuery(window).bind("lalaHistoryChange", function(b) {
    if (b.href == "country") {

        Country.build();

    }
});

if (typeof (Country) == "undefined") {
    Country = {}
}

Country.build = function(c) {

    Utils.HideSections("#song_list");
    $("#right").css("background", "#141318");
    var b = Templates.select_country;
    //jQuery("#song_list").html(b);
    Country.Display.request()
};


Country.Display = {
    method: player_root + "more.php?t=country",
    request: function() {
        //                        alert("gihy");die;
        var d = Country.Display.method;
        jQuery.ajax({
            url: d,
            type: "GET",
            dataType: "json",
            complete: Country.Display.response,
            cache: false
        })

    },
    response: function(b, d) {
        jQuery(this).css("opacity", 1);
        var c = Utils.APIResponse(b, "Country.Display.response");
        if (b.status == 200) {
            Country.BuildList(c.json.buffers);

        }
    }
}
Country.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.select_country;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b

            })
        }
    }

    jQuery("#song_list").html(d);

};

jQuery(document).on('click', '.country_song', function(e) {//.bind('.country_song').click(function(e){
    //    console.log($(this).html());
    var src = $(this).attr('id');
    jQuery(".select_country").addClass('display_none');
    jQuery.ajax({
        url: "/setsession.php",
        type: "GET",
        data: {
            countryid: src
        },
        dataType: "json",
//        complete: Country.Display.response,
        success: function(response) {
            //   alert(response);
        }
    })

// alert(src);
//
//sessionStorage.id = src;
//alert(sessionStorage.id);

//sessionStorage.clear();
});
jQuery(window).bind("lalaHistoryChange", function(b) {
    if (b.href == "country") {

        Country.build();

    }
});

if (typeof (Country) == "undefined") {
    Country = {}
}

Country.build = function(c) {

    Utils.HideSections("#song_list");
    $("#right").css("background", "#141318");
    var b = Templates.select_country;
    //jQuery("#song_list").html(b);
    Country.Display.request()
};


Country.Display = {
    method: player_root + "more.php?t=country",
    request: function() {
        //                        alert("gihy");die;
        var d = Country.Display.method;
        jQuery.ajax({
            url: d,
            type: "GET",
            dataType: "json",
            complete: Country.Display.response,
            cache: false
        })

    },
    response: function(b, d) {
        jQuery(this).css("opacity", 1);
        var c = Utils.APIResponse(b, "Country.Display.response");
        if (b.status == 200) {
            Country.BuildList(c.json.buffers);

        }
    }
}
Country.BuildList = function(f) {
    var d = "";
    var a = f.length;
    if (a > 0) {
        var e = Templates.select_country;
        for (var c = 0; c < a; c++) {
            var b = f[c];
            d += e({
                item: b

            })
        }
    }

    jQuery("#song_list").html(d);

};

jQuery(document).on('click', '.country_song', function(e) {//.bind('.country_song').click(function(e){
    //    console.log($(this).html());
    var src = $(this).attr('id');
    jQuery(".select_country").addClass('display_none');
    jQuery.ajax({
        url: "/setsession.php",
        type: "GET",
        data: {
            countryid: src,
            countryavail: false
        },
        dataType: "json",
//        complete: Country.Display.response,
        success: function(response) {
            //   alert(response);
        }
    })

// alert(src);
//
//sessionStorage.id = src;
//alert(sessionStorage.id);

//sessionStorage.clear();
});

$('a#Default_country').click(function() {

    var a = loggedInUser.user_id;
    if (loggedInUser.user_id != null) {
        jQuery.ajax({
            url: "/setsession.php",
            type: "GET",
            data: {
                countryavail: true
            },
            success: function() {
                location.reload();
            }
        })
    } else {
        alert("You must login to use this feature!", true)
    }

});

var Base64 = {
    _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
    encode: function(input) {
        var output = "";
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
        var i = 0;

        input = Base64._utf8_encode(input);

        while (i < input.length) {

            chr1 = input.charCodeAt(i++);
            chr2 = input.charCodeAt(i++);
            chr3 = input.charCodeAt(i++);

            enc1 = chr1 >> 2;
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
            enc4 = chr3 & 63;

            if (isNaN(chr2)) {
                enc3 = enc4 = 64;
            } else if (isNaN(chr3)) {
                enc4 = 64;
            }

            output = output + this._keyStr.charAt(enc1) + this._keyStr.charAt(enc2) + this._keyStr.charAt(enc3) + this._keyStr.charAt(enc4);

        }

        return output;
    },
    decode: function(input) {
        var output = "";
        var chr1, chr2, chr3;
        var enc1, enc2, enc3, enc4;
        var i = 0;

        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

        while (i < input.length) {

            enc1 = this._keyStr.indexOf(input.charAt(i++));
            enc2 = this._keyStr.indexOf(input.charAt(i++));
            enc3 = this._keyStr.indexOf(input.charAt(i++));
            enc4 = this._keyStr.indexOf(input.charAt(i++));

            chr1 = (enc1 << 2) | (enc2 >> 4);
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
            chr3 = ((enc3 & 3) << 6) | enc4;

            output = output + String.fromCharCode(chr1);

            if (enc3 != 64) {
                output = output + String.fromCharCode(chr2);
            }
            if (enc4 != 64) {
                output = output + String.fromCharCode(chr3);
            }

        }

        output = Base64._utf8_decode(output);

        return output;

    },
    _utf8_encode: function(string) {
        string = string.replace(/\r\n/g, "\n");
        var utftext = "";

        for (var n = 0; n < string.length; n++) {

            var c = string.charCodeAt(n);

            if (c < 128) {
                utftext += String.fromCharCode(c);
            }
            else if ((c > 127) && (c < 2048)) {
                utftext += String.fromCharCode((c >> 6) | 192);
                utftext += String.fromCharCode((c & 63) | 128);
            }
            else {
                utftext += String.fromCharCode((c >> 12) | 224);
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                utftext += String.fromCharCode((c & 63) | 128);
            }

        }

        return utftext;
    },
    _utf8_decode: function(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while (i < utftext.length) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if ((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i + 1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i + 1);
                c3 = utftext.charCodeAt(i + 2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    }

}



function getCookieData(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');

    //console.log(ca);
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return "";
} 






   