function $(id){return document.getElementById(id);}

if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function(elt) {
		var len = this.length;
		var from = Number(arguments[1]) || 0;
		from = (from < 0)
			? Math.ceil(from)
			: Math.floor(from);
		if (from < 0)
		from += len;

		for (; from < len; from++) {
			if (from in this && this[from] === elt)
			return from;
		}
		return -1;
	};
}

var RegisteredObjectNames = new Array();

function createCookie(name,value,days) {
	if(RegisteredObjectNames.indexOf(name)<0 && days!=-1) {
		RegisteredObjectNames.push(name);
	}
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*86400000));
		var expires = ";expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+encodeURIComponent(value)+expires+";path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) {
			if(RegisteredObjectNames.indexOf(name)<0) {
				RegisteredObjectNames.push(name);
			}
			return decodeURIComponent(c.substring(nameEQ.length,c.length));
		}
	}
	return null;
}

function eraseCookie(name) {
	try{RegisteredObjectNames.splice(RegisteredObjectNames.indexOf(name),1);}catch(e){}
	createCookie(name,"",-1);
}

function SetObjectValue(Obj,d) {
	if(Obj.type=="select-one") {
		for(var i=0;i<Obj.length;i++) {
			if(Obj.options[i].value==d) {
				d=Obj.options[i].index;
				Obj.selectedIndex=d;
				break;
			}
		}
	}
	else if(Obj.type=="checkbox") {
		if(d){d="checked";}else{d="";}
		Obj.checked=(d);
	} else {
		Obj.value=d;
	}
}

function SetObjectValueID(id,d) {
	SetObjectValue($(id),d);
}

function deleteAllCookies() {
	while(RegisteredObjectNames.length>0) {
		eraseCookie(RegisteredObjectNames[0]);
	}
}

function GetInputObjCookie(Obj) {
	var d=readCookie(Obj.id);
	if(d==null) {
		d=Obj.value;
	}
	SetObjectValue(Obj,d);
	return d;
}

function GetCheckBoxObjCookie(Obj) {
	var d=readCookie(Obj.id);
	if(d==null) {
		d=""+Obj.checked;
	}
	d=(d=="true");
	SetObjectValue(Obj,d);
	return d;
}

function GetSelectObjCookie(Obj) {
	var d=readCookie(Obj.id);
	if(d==null) {
		d=Obj.value;
	}
	SetObjectValue(Obj,d);
	return d;
}

function GetObjCookie(Obj) {
	var t=Obj.type;
	if(t=="text" || t=="hidden"){return GetInputObjCookie(Obj);}
	if(t=="select-one"){return GetSelectObjCookie(Obj);}
	if(t=="checkbox"){return GetCheckBoxObjCookie(Obj);}
	return null;
}

function SetInputObjCookie(Obj) {
	var d=Obj.value;
	createCookie(Obj.id,d);
}

function SetCheckBoxObjCookie(Obj) {
	var d=""+(Obj.checked);
	createCookie(Obj.id,d);
}

function SetSelectObjCookie(Obj) {
	var d=Obj.value;
	createCookie(Obj.id,d);
}

function SetObjCookie(Obj) {
	var t=Obj.type;
	if(t=="text" || t=="hidden"){SetInputObjCookie(Obj);}
	if(t=="select-one"){SetSelectObjCookie(Obj);}
	if(t=="checkbox"){SetCheckBoxObjCookie(Obj);}
}

function EraseInputObjCookie(Obj) {
	var d=readCookie(Obj.id);
	eraseCookie(Obj.id);
	if(d==null) {
		d=Obj.value;
	}
	Obj.value=d;
	return d;
}

function EraseCheckBoxObjCookie(Obj) {
	var d=readCookie(Obj.id);
	eraseCookie(Obj.id);
	if(d==null) {
		d=""+Obj.checked;
	}
	d=(d=="true");
	Obj.checked=d;
	return d;
}

function EraseSelectObjCookie(Obj) {
	var d=readCookie(Obj.id);
	eraseCookie(Obj.id);
	if(d==null) {
		d=Obj.value;
	}
	for(var i=0;i<Obj.length;i++) {
		if(Obj.options[i].value==d) {
			d=Obj.options[i].index;
			Obj.selectedIndex=d;
			return Obj.options[i].value;
		}
	}
	return d;
}

function EraseObjCookie(Obj) {
	var t=Obj.type;
	if(t=="text" || t=="hidden"){return EraseInputObjCookie(Obj);}
	if(t=="select-one"){return EraseSelectObjCookie(Obj);}
	if(t=="checkbox"){return EraseCheckBoxObjCookie(Obj);}
	return null;
}

var KillDoUpdate=false;
var KillDoUpdateStack=new Array(0);
function PushUpdateKill() {
	KillDoUpdateStack.push(KillDoUpdate);
	KillDoUpdate=true;
}

function PopUpdateKill() {
	if(KillDoUpdateStack.length>0) {
		KillDoUpdate=KillDoUpdateStack.pop();
	} else {
		KillDoUpdate=false;
	}
}

function GetAllSettings() {
	var Res="";
	for(var i=0;i < RegisteredObjectNames.length;i++) {
		var name = RegisteredObjectNames[i];
		var Obj=$(name);
		if(Obj==null || Obj==undefined)continue;
		Res+=name+"="+(encodeURI(GetObjCookie(Obj)))+"\r\n";
	}
	return Res;
}

function CallObjectChanged(Obj) {
	if(Obj==null || Obj==undefined)return;
	var fe=(Obj.onchange || Obj.onclick);
	if(fe==null || fe==undefined)return;
	evt=new Event(Obj);
	evt.target=Obj;
	evt.srcElement=Obj;
	var oe=window.event;
	try {
		window.event=evt;
	}
	catch(e) {
		fe(evt);
		delete evt;
		return;
	}
	fe(evt);
	window.event=oe;
	delete evt;
}

function PutAllSettings(data) {
	PushUpdateKill();
	deleteAllCookies();
	data=data.split("\r\n");
	data=data.join(";");
	data=data.split("\r");
	data=data.join(";");
	data=data.split("\n");
	data=data.join(";");
	data=data.split(";");
	var datafail = new Array;
	var FailPass=0;
	while(FailPass<2) {
		for(var i=0;i<data.length;i++) {
			var t=data[i];
			t=t.split("=");
			var z=t.shift();if(z==""){continue;}
			t=t.join("=");
			var a=decodeURI(t);
			var vi=$(z);
			if(vi!=null && vi!=undefined) {
				if(vi.type=="button") {
					continue;
				}
				createCookie(z,a);
				GetObjCookie(vi);
				CallObjectChanged(vi);
			} else {
				if(FailPass<1) {
					datafail.push(z+"="+t);
				}
			}
		}
		delete data;
		if(FailPass==0)
		{
			data=datafail;datafail=null;
		}
		FailPass++;
	}
	if(data!=null)delete data;
	PopUpdateKill();
	DoUpdate();
}

function RetoolPathDelimiters(Obj,nd) {
	var t=new String(Obj.value);
	var ot=t;
	var od="\\";
	if(od==nd){od="/";}
	if(t.indexOf(nd)!=-1)return;
	var a=t.split(od);
	t=a.join(nd);
	if(t!=ot) {
		SetObjectValue(Obj,t);
		SetObjCookie(Obj);
		CallObjectChanged(Obj);
	}
}

var linux="\n";
var windows="\r\n";
var macintosh="\r";

var linuxPD="/";
var windowsPD="\\";
var macintoshPD="/";

// this holds the collapsed state of the page sections (change value if more added / removed)
var CollapsedArrayNum = 21;
var CollapsedArray = new Array(CollapsedArrayNum);
var CPD;
var NumOfPlaylists=0;
var PlaylistMultiSymNameArray = new Array(NumOfPlaylists);
var PlaylistMultiFileArray = new Array(NumOfPlaylists);
var BufferType;
var AdaptiveBufferSize;
var FixedBufferSize;
var BufferHardLimit;
var MaxHeaderLineSize;
var MaxHeaderLineCount;
var NameLookups;
var Platform;
var Config;
var DNASBasePath;
var TranscoderBasePath;
var DNASConfFile;
var TransConfFile;
//var Mode;
var DNASPublic;
var SCTransPublic;
var MetaInterval;
var YPAddr;
var YPPort;
var YPPath;
var YPTimeout;
var YPMaxRetries;
var YPReportInterval;
var YPMinReportInterval;
var PortBase;
var NumOfEndPoints;
var NumOfCalendarEvents=0;
var CalendarEventTypeArray = new Array(NumOfCalendarEvents);
var CalendarDJArchiveArray = new Array(NumOfCalendarEvents);
var CalendarDJNameArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistLoopAtEndArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistShuffleArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistPriorityArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistNameArray = new Array(NumOfCalendarEvents);
var CalendarRelayURLArray = new Array(NumOfCalendarEvents);
var CalendarRelayPriorityArray = new Array(NumOfCalendarEvents);
var CalendarPerArray = new Array(NumOfCalendarEvents);
var CalendarSunArray = new Array(NumOfCalendarEvents);
var CalendarMonArray = new Array(NumOfCalendarEvents);
var CalendarTueArray = new Array(NumOfCalendarEvents);
var CalendarWedArray = new Array(NumOfCalendarEvents);
var CalendarThuArray = new Array(NumOfCalendarEvents);
var CalendarFriArray = new Array(NumOfCalendarEvents);
var CalendarSatArray = new Array(NumOfCalendarEvents);
var CalendarStartDateArray = new Array(NumOfCalendarEvents);
var CalendarEndDateArray = new Array(NumOfCalendarEvents);
var CalendarStartTimeArray = new Array(NumOfCalendarEvents);
var CalendarDurationArray = new Array(NumOfCalendarEvents);
var CalendarTimeOffsetArray = new Array(NumOfCalendarEvents);
var DNASIP;
var SourceBindAddress;
var DestinationBindAddress;
var TransAdminPort;
var Password;
var AdminPassword;
var VUImageDirectory;
var VUImageSuffix;
var VUImageMimeType;
var DNASFlashPolicyFile;
var DNASFlashPolicyServerPort;
var FlashPolicyFile;
var FlashPolicyServerPort;
var BanFile;
var RipFile;
var RipOnly;
var MaxListeners;
var ListenerTime;
var AutoDumpUsers;
var BuilderViewMode;
var GenerateMinimal;
var TranscoderInherit;
var DNASDebugMode;
var TransDebugMode;
var YP1Debug;
var YP2Debug;
var SHOUTcastSourceDebug;
var UVOX2SourceDebug;
var SHOUTcast1ClientDebug;
var SHOUTcast2ClientDebug;
var RelaySHOUTcastDebug;
var RelayUVOXDebug;
var RelayDebug;
var StreamDataDebug;
var HTTPStyleDebug;
var StatsDebug;
var MicroServerDebug;
var ThreadRunnerDebug;
var RTMPClientDebug;
var ShuffleDebug;
var SHOUTcastDebug;
var UVOXDebug;
var GainDebug;
var PlaylistDebug;
var MP3EncDebug;
var MP3DecDebug;
var ResamplerDebug;
var RGCalcDebug;
var APIDebug;
var CalendarDebug;
var CaptureDebug;
var DJDebug;
var FlashPolicyServerDebug;
var FileConverterDebug;
var SourceRelayDebug;
var SourceAndEndpointManagerDebug;
var StreamTitle;
var StreamURL;
var StreamGenre;
var AIM;
var IRC;
var ICQ;
var UseMetadata;
var MetadataPattern;
var DisplayMetadataPattern;
var TitleFormat;
var URLFormat;
var Screenlog;
var ClientConnectLog;
var EnableLogging;
var DNASLogFile;
var TransLogFile;
var W3CLogging;
var W3CLogFile;
var WebClientDebug;
var CalendarFile;
var CalendarEnableRewrite;
var CalendarEventAddButton;
var PlaylistFile;
var EnableShuffle;
var XFadeTime;
var XFadeThreshold;
var PlaylistFolder;
var PlaylistArchiveFolder;
var DJPort;
var DJPort2;
var DJCipher;
var DJAutoDumpSourceTime;
var DJCaptureEnable;
var DJBroadcastsPath;
var DJFilePattern;
var NumOfDJs=0;
var DJLoginArray = new Array(NumOfDJs);
var DJPasswordArray = new Array(NumOfDJs);
var DJPriorityArray = new Array(NumOfDJs);
var EnableCapture;
var CaptureDevice;
var CaptureInput;
var CaptureSampleRate;
var CaptureNumChannels;
var ApplyReplayGain;
var DefaultReplayGain;
var DJReplayGain;
var CaptureReplayGain;
var CalculateReplayGain;
var ReplayGainTempFolder;
var ReplayGainRunAhead;
var ReplayGainDontWrite;
var EnhanceReplayGain;
var MP3UnlockKeyName;
var MP3UnlockKeyCode;
var DefaultAuthHash;
var DNASConfigreWrite;
var TransConfigreWrite;
var DNASAdminPageTheme;
var DNASAdminPageFavIcon;
var DNASAdminPageFavIconMimeType;
var HideStats;
var DNASIntroFile;
var DNASBackupFile;
var MaxSpecialFileSize;
var ServerBackupFile;
var ServerIntroFile;
var SongHistory;

function SetDefaults() {
	delete CollapsedArray;CollapsedArray = new Array(CollapsedArrayNum);for(var i=0;i<CollapsedArrayNum;i++){CollapsedArray[i]=0;}
	CPD=windowsPD;
	NumOfPlaylists=0;SetObjectValueID("NumOfPlaylistsHidden",NumOfPlaylists);
	delete PlaylistMultiSymNameArray;PlaylistMultiSymNameArray = new Array(NumOfPlaylists);for(var i=0;i<NumOfPlaylists;i++){PlaylistMultiSymNameArray[i]="";}
	delete PlaylistMultiFileArray;PlaylistMultiFileArray = new Array(NumOfPlaylists);for(var i=0;i<NumOfPlaylists;i++){PlaylistMultiFileArray[i]="";}
	BufferType=0;SetObjectValueID("BufferTypeSelect",BufferType);
	AdaptiveBufferSize=1;SetObjectValueID("AdaptiveBufferSizeInput",AdaptiveBufferSize);
	FixedBufferSize=1048576;SetObjectValueID("FixedBufferSizeInput",FixedBufferSize);
	BufferHardLimit=16777216;SetObjectValueID("BufferHardLimitInput",BufferHardLimit);
	MaxHeaderLineSize=2048;SetObjectValueID("MaxHeaderLineSizeInput",MaxHeaderLineSize);
	MaxHeaderLineCount=100;SetObjectValueID("MaxHeaderLineCountInput",MaxHeaderLineCount);
	NameLookups=false;SetObjectValueID("NameLookupsCheckBox",NameLookups);
	Platform=1;SetObjectValueID("PlatformSelect",Platform);
	Config=0;SetObjectValueID("ConfigSelect",Config);
	DNASBasePath="";SetObjectValueID("DNASBasePathInput",DNASBasePath);
	TranscoderBasePath="";SetObjectValueID("TranscoderBasePathInput",TranscoderBasePath);
	DNASConfFile="sc_serv.conf";SetObjectValueID("DNASConfFileInput",DNASConfFile);
	TransConfFile="sc_trans.conf";SetObjectValueID("TransConfFileInput",TransConfFile);
	//Mode=2;SetObjectValueID("ModeSelect",Mode);
	DNASPublic="default";SetObjectValueID("DNASPublicSelect",DNASPublic);
	SCTransPublic=false;SetObjectValueID("TransPublicCheckBox",SCTransPublic);
	MetaInterval=8192;SetObjectValueID("MetaIntervalInput",MetaInterval);
	YPAddr="yp.shoutcast.com";SetObjectValueID("YPAddrInput",YPAddr);
	YPPort=80;SetObjectValueID("YPPortInput",YPPort);
	YPPath="/yp2";SetObjectValueID("YPPathInput",YPPath);
	YPTimeout=60;SetObjectValueID("YPTimeoutInput",YPTimeout);
	YPMaxRetries=10;SetObjectValueID("YPMaxRetriesInput",YPMaxRetries);
	YPReportInterval=300;SetObjectValueID("YPReportIntervalInput",YPReportInterval);
	YPMinReportInterval=10;SetObjectValueID("YPMinReportIntervalInput",YPMinReportInterval);
	PortBase=8000;SetObjectValueID("PortBaseInput",PortBase);
	NumOfEndPoints=1;SetObjectValueID("NumOfEndPointsInput",NumOfEndPoints);
	DNASIP="localhost";SetObjectValueID("DNASIPInput",DNASIP);
	RobotsTxt="";SetObjectValueID("RobotsTxtInput",RobotsTxt);
	SourceBindAddress="";SetObjectValueID("SourceBindAddressInput",SourceBindAddress);
	DestinationBindAddress="";SetObjectValueID("DestinationBindAddressInput",DestinationBindAddress);
	TransAdminPort=0;SetObjectValueID("TransAdminPortInput",TransAdminPort);
	Password="******";SetObjectValueID("PasswordInput",Password);
	AdminPassword="**********";SetObjectValueID("AdminPasswordInput",AdminPassword);
	VUImageDirectory="vuimages\\";SetObjectValueID("VUImageDirectoryInput",VUImageDirectory);
	VUImageSuffix="png";SetObjectValueID("VUImageSuffixInput",VUImageSuffix);
	VUImageMimeType="image/png";SetObjectValueID("VUImageMimeTypeInput",VUImageMimeType);
	DNASFlashPolicyFile="crossdomain.xml";SetObjectValueID("DNASFlashPolicyFileInput",DNASFlashPolicyFile);
	FlashPolicyFile="crossdomain.xml";SetObjectValueID("FlashPolicyFileInput",FlashPolicyFile);
	FlashPolicyServerPort=0;SetObjectValueID("FlashPolicyServerPortInput",FlashPolicyServerPort);
	DNASFlashPolicyServerPort=0;SetObjectValueID("DNASFlashPolicyServerPortInput",DNASFlashPolicyServerPort);
	BanFile="sc_serv.ban";SetObjectValueID("BanFileInput",BanFile);
	RipFile="sc_serv.rip";SetObjectValueID("RipFileInput",RipFile);
	RipOnly=false;SetObjectValueID("RipOnlyCheckBox",RipOnly);
	MaxListeners=32;SetObjectValueID("MaxListenersInput",MaxListeners);
	ListenerTime=0;SetObjectValueID("ListenerTimeInput",ListenerTime);
	AutoDumpUsers=false;SetObjectValueID("AutoDumpUsersCheckBox",AutoDumpUsers);
	YP1Debug=false;SetObjectValueID("YP1DebugCheckBox",YP1Debug);
	YP2Debug=false;SetObjectValueID("YP2DebugCheckBox",YP2Debug);
	SHOUTcastSourceDebug=false;SetObjectValueID("SHOUTcastSourceDebugCheckBox",SHOUTcastSourceDebug);
	UVOX2SourceDebug=false;SetObjectValueID("UVOX2SourceDebugCheckBox",UVOX2SourceDebug);
	SHOUTcast1ClientDebug=false;SetObjectValueID("SHOUTcast1ClientDebugCheckBox",SHOUTcast1ClientDebug);
	SHOUTcast2ClientDebug=false;SetObjectValueID("SHOUTcast2ClientDebugCheckBox",SHOUTcast2ClientDebug);
	RelaySHOUTcastDebug=false;SetObjectValueID("RelaySHOUTcastDebugCheckBox",RelaySHOUTcastDebug);
	RelayUVOXDebug=false;SetObjectValueID("RelayUVOXDebugCheckBox",RelayUVOXDebug);
	RelayDebug=false;SetObjectValueID("RelayDebugCheckBox",RelayDebug);
	StreamDataDebug=false;SetObjectValueID("StreamDataDebugCheckBox",StreamDataDebug);
	HTTPStyleDebug=false;SetObjectValueID("HTTPStyleDebugCheckBox",HTTPStyleDebug);
	StatsDebug=false;SetObjectValueID("StatsDebugCheckBox",StatsDebug);
	MicroServerDebug=false;SetObjectValueID("MicroServerDebugCheckBox",MicroServerDebug);
	ThreadRunnerDebug=false;SetObjectValueID("ThreadRunnerDebugCheckBox",ThreadRunnerDebug);
	RTMPClientDebug=false;SetObjectValueID("RTMPClientDebugCheckBox",RTMPClientDebug);
	BuilderViewMode=0;SetObjectValueID("BuilderViewModeHidden",BuilderViewMode);
	GenerateMinimal=1;SetObjectValueID("GenerateMinimalCheckBox",GenerateMinimal);
	TranscoderInherit=1;SetObjectValueID("TranscoderInheritCheckBox",TranscoderInherit);
	DNASDebugMode=0;SetObjectValueID("DNASDebugModeHidden",DNASDebugMode);
	TransDebugMode=0;SetObjectValueID("TransDebugModeHidden",TransDebugMode);
	ShuffleDebug=false;SetObjectValueID("ShuffleDebugCheckBox",ShuffleDebug);
	SHOUTcastDebug=false;SetObjectValueID("SHOUTcastDebugCheckBox",SHOUTcastDebug);
	UVOXDebug=false;SetObjectValueID("UVOXDebugCheckBox",UVOXDebug);
	GainDebug=false;SetObjectValueID("GainDebugCheckBox",GainDebug);
	PlaylistDebug=false;SetObjectValueID("PlaylistDebugCheckBox",PlaylistDebug);
	MP3EncDebug=false;SetObjectValueID("MP3EncDebugCheckBox",MP3EncDebug);
	MP3DecDebug=false;SetObjectValueID("MP3DecDebugCheckBox",MP3DecDebug);
	ResamplerDebug=false;SetObjectValueID("ResamplerDebugCheckBox",ResamplerDebug);
	RGCalcDebug=false;SetObjectValueID("RGCalcDebugCheckBox",RGCalcDebug);
	APIDebug=false;SetObjectValueID("APIDebugCheckBox",APIDebug);
	CalendarDebug=false;SetObjectValueID("CalendarDebugCheckBox",CalendarDebug);
	CaptureDebug=false;SetObjectValueID("CaptureDebugCheckBox",CaptureDebug);
	DJDebug=false;SetObjectValueID("DJDebugCheckBox",DJDebug);
	FlashPolicyServerDebug=false;SetObjectValueID("FlashPolicyServerDebugCheckBox",FlashPolicyServerDebug);
	FileConverterDebug=false;SetObjectValueID("FileConverterDebugCheckBox",FileConverterDebug);
	SourceRelayDebug=false;SetObjectValueID("SourceRelayDebugCheckBox",SourceRelayDebug);
	SourceAndEndpointManagerDebug=false;SetObjectValueID("SourceAndEndpointManagerDebugCheckBox",SourceAndEndpointManagerDebug);
	StreamTitle="Unnamed Server";SetObjectValueID("StreamTitleInput",StreamTitle);
	StreamURL="http://www.shoutcast.com/";SetObjectValueID("StreamURLInput",StreamURL);
	StreamGenre="Misc";SetObjectValueID("StreamGenreInput",StreamGenre);
	AIM="";SetObjectValueID("AIMInput",AIM);
	IRC="";SetObjectValueID("IRCInput",IRC);
	ICQ="";SetObjectValueID("ICQInput",ICQ);
	UseMetadata=true;SetObjectValueID("UseMetadataCheckBox",UseMetadata);
	MetadataPattern="";SetObjectValueID("MetadataPatternInput",MetadataPattern);
	DisplayMetadataPattern="";SetObjectValueID("DisplayMetadataPatternInput",DisplayMetadataPattern);
	TitleFormat="";SetObjectValueID("TitleFormatInput",TitleFormat);
	URLFormat="";SetObjectValueID("URLFormatInput",URLFormat);
	Screenlog=true;SetObjectValueID("ScreenlogCheckBox",Screenlog);
	ClientConnectLog=true;SetObjectValueID("ClientConnectCheckBox",ClientConnectLog);
	EnableLogging=true;SetObjectValueID("EnableLoggingCheckBox",EnableLogging);
	DNASLogFile="sc_serv.log";SetObjectValueID("DNASLogFileInput",DNASLogFile);
	TransLogFile="sc_trans.log";SetObjectValueID("TransLogFileInput",TransLogFile);
	W3CLogging=true;SetObjectValueID("W3CLoggingCheckBox",W3CLogging);
	W3CLogFile="sc_w3c.log";SetObjectValueID("W3CLogFileInput",W3CLogFile);
	WebClientDebug=false;SetObjectValueID("WebClientDebugCheckBox",WebClientDebug);
	CalendarFile="calendar.xml";SetObjectValueID("CalendarFileInput",CalendarFile);
	CalendarEnableRewrite=false;SetObjectValueID("CalendarEnableRewriteCheckBox",CalendarEnableRewrite);
	PlaylistFile="playlist.lst";SetObjectValueID("PlaylistFileInput",PlaylistFile);
	EnableShuffle=false;SetObjectValueID("EnableShuffleCheckBox",EnableShuffle);
	XFadeTime=1;SetObjectValueID("XFadeTimeInput",XFadeTime);
	XFadeThreshold=10;SetObjectValueID("XFadeThresholdInput",XFadeThreshold);
	PlaylistFolder="playlist\\";SetObjectValueID("PlaylistFolderInput",PlaylistFolder);
	PlaylistArchiveFolder="archived\\";SetObjectValueID("PlaylistArchiveFolderInput",PlaylistArchiveFolder);
	DJPort=0;SetObjectValueID("DJPortInput",DJPort);
	DJPort2=0;SetObjectValueID("DJPort2Input",DJPort2);
	DJCipher="";SetObjectValueID("DJCipherInput",DJCipher);
	DJAutoDumpSourceTime=30;SetObjectValueID("DJAutoDumpSourceTimeInput",DJAutoDumpSourceTime);
	DJCaptureEnable=false;SetObjectValueID("DJCaptureEnableCheckBox",DJCaptureEnable);
	DJBroadcastsPath="recorded\\";SetObjectValueID("DJBroadcastsPathInput",DJBroadcastsPath);
	DJFilePattern="";SetObjectValueID("DJFilePatternInput",DJFilePattern);
	NumOfDJs=0;SetObjectValueID("NumOfDJsHidden",NumOfDJs);
	delete DJLoginArray;DJLoginArray = new Array(NumOfDJs);for(var i=0;i<NumOfDJs;i++){DJLoginArray[i]="";}
	delete DJPasswordArray;DJPasswordArray = new Array(NumOfDJs);for(var i=0;i<NumOfDJs;i++){DJPasswordArray[i]="";}
	delete DJPriorityArray;DJPriorityArray = new Array(NumOfDJs);for(var i=0;i<NumOfDJs;i++){DJPriorityArray[i]=1;}
	NumOfCalendarEvents=0;SetObjectValueID("NumOfCalendarEventsHidden",NumOfCalendarEvents);
	delete CalendarEventTypeArray;CalendarEventTypeArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarEventTypeArray[i]="";}
	delete CalendarDJArchiveArray;CalendarDJArchiveArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarDJArchiveArray[i]="";}
	delete CalendarDJNameArray;CalendarDJNameArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarDJNameArray[i]="";}
	delete CalendarPlaylistLoopAtEndArray;CalendarPlaylistLoopAtEndArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarPlaylistLoopAtEndArray[i]="";}
	delete CalendarPlaylistShuffleArray;CalendarPlaylistShuffleArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarPlaylistShuffleArray[i]="";}
	delete CalendarPlaylistPriorityArray;CalendarPlaylistPriorityArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarPlaylistPriorityArray[i]="";}
	delete CalendarPlaylistNameArray;CalendarPlaylistNameArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarPlaylistNameArray[i]="";}
	delete CalendarRelayURLArray;CalendarRelayURLArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarRelayURLArray[i]="";}
	delete CalendarRelayPriorityArray;CalendarRelayPriorityArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarRelayPriorityArray[i]="";}
	delete CalendarPerArray;CalendarPerArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarPerArray[i]="";}
	delete CalendarSunArray;CalendarSunArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarSunArray[i]="";}
	delete CalendarMonArray;CalendarMonArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarMonArray[i]="";}
	delete CalendarTueArray;CalendarTueArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarTueArray[i]="";}
	delete CalendarWedArray;CalendarWedArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarWedArray[i]="";}
	delete CalendarThuArray;CalendarThuArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarThuArray[i]="";}
	delete CalendarFriArray;CalendarFriArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarFriArray[i]="";}
	delete CalendarSatArray;CalendarSatArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarSatArray[i]="";}
	delete CalendarStartDateArray;CalendarStartDateArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarStartDateArray[i]="";}
	delete CalendarEndDateArray;CalendarEndDateArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarEndDateArray[i]="";}
	delete CalendarStartTimeArray;CalendarStartTimeArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarStartTimeArray[i]="";}
	delete CalendarDurationArray;CalendarDurationArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarDurationArray[i]="";}
	delete CalendarTimeOffsetArray;CalendarTimeOffsetArray = new Array(NumOfCalendarEvents);for(var i=0;i<NumOfCalendarEvents;i++){CalendarTimeOffsetArray[i]="";}
	EnableCapture=false;SetObjectValueID("EnableCaptureCheckBox",EnableCapture);
	CaptureDevice="";SetObjectValueID("CaptureDeviceInput",CaptureDevice);
	CaptureInput="";SetObjectValueID("CaptureInputInput",CaptureInput);
	CaptureSampleRate="44100";SetObjectValueID("CaptureSampleRateInput",CaptureSampleRate);
	CaptureNumChannels=2;SetObjectValueID("CaptureNumChannelsInput",CaptureNumChannels);
	ApplyReplayGain=false;SetObjectValueID("ApplyReplayGainCheckBox",ApplyReplayGain);
	DefaultReplayGain="0.0";SetObjectValueID("DefaultReplayGainInput",DefaultReplayGain);
	DJReplayGain="0.0";SetObjectValueID("DJReplayGainInput",DJReplayGain);
	CaptureReplayGain="0.0";SetObjectValueID("CaptureReplayGainInput",CaptureReplayGain);
	CalculateReplayGain=false;SetObjectValueID("CalculateReplayGainCheckBox",CalculateReplayGain);
	ReplayGainTempFolder="";SetObjectValueID("ReplayGainTempFolderInput",ReplayGainTempFolder);
	ReplayGainRunAhead=2;SetObjectValueID("ReplayGainRunAheadInput",ReplayGainRunAhead);
	ReplayGainDontWrite=false;SetObjectValueID("ReplayGainDontWriteCheckBox",ReplayGainDontWrite);
	EnhanceReplayGain="6.0";SetObjectValueID("EnhanceReplayGainInput",EnhanceReplayGain);
	MP3UnlockKeyName="";SetObjectValueID("MP3UnlockKeyNameInput",MP3UnlockKeyName);
	MP3UnlockKeyCode="";SetObjectValueID("MP3UnlockKeyCodeInput",MP3UnlockKeyCode);
	DefaultAuthHash="";SetObjectValueID("DefaultAuthHashInput",DefaultAuthHash);
	DNASConfigreWrite=false;SetObjectValueID("DNASConfigreWriteCheckBox",DNASConfigreWrite);
	TransConfigreWrite=false;SetObjectValueID("TransConfigreWriteCheckBox",TransConfigreWrite);
	DNASAdminPageTheme="v2";SetObjectValueID("DNASAdminPageThemeInput",DNASAdminPageTheme);
	DNASAdminPageFavIcon="";SetObjectValueID("DNASAdminPageFavIconInput",DNASAdminPageFavIcon);
	DNASAdminPageFavIconMimeType="image/x-icon";SetObjectValueID("DNASAdminPageFavIconMimeTypeInput",DNASAdminPageFavIconMimeType);
	HideStats=false;SetObjectValueID("HideStatsCheckBox",HideStats);
	DNASIntroFile="";SetObjectValueID("DNASIntroFileInput",DNASIntroFile);
	DNASBackupFile="";SetObjectValueID("DNASBackupFileInput",DNASBackupFile);
	MaxSpecialFileSize=30000000;SetObjectValueID("MaxSpecialFileSizeInput",MaxSpecialFileSize);
	ServerBackupFile="";SetObjectValueID("ServerBackupFileInput",ServerBackupFile);
	ServerIntroFile="";SetObjectValueID("ServerIntroFileInput",ServerIntroFile);
	SongHistory=10;SetObjectValueID("SongHistoryInput",SongHistory);
}

var scscl="";
var sctcl="";
var scccl="";
var scssl="";
var sctsl="";
var scssfn="";
var sctsfn="";

var myScrollTable;
//var lastHeight=0;
var HelperTable;
var HelperSpan;
var HelperXMLHTTP;
var HelperTextDBKeysArray;
var HelperTextDBValuesArray;

var SCServLinesTextArea;
var SCTransLinesTextArea;
var SCCalendarLinesTextArea;
var AddPlaylistButton;
var MultiPlaylistSpan;
var NumOfPlaylistsHidden;
var PlaylistMultiRemoveButtonArray = new Array(NumOfPlaylists);
var PlaylistMultiSymNameInputArray = new Array(NumOfPlaylists);
var PlaylistMultiCalendarAddButtonArray = new Array(NumOfPlaylists);
var PlaylistMultiFileInputArray = new Array(NumOfPlaylists);
var BufferTypeSelect;
var AdaptiveBufferSizeInput;
var AdaptiveBufferSizeTR;
var FixedBufferSizeTR;
var FixedBufferSizeInput;
var BufferHardLimitInput;
var MaxHeaderLineSizeInput;
var MaxHeaderLineCountInput;
var NameLookupsCheckBox;
var PlatformSelect;
var ConfigSelect;
var DNASBasePathInput;
var TranscoderBasePathInput;
var DNASConfFileInput;
var TransConfFileInput;
//var ModeSelect;
var EnableLoggingCheckBox;
var ScreenlogCheckBox;
var DNASLogFileInput;
var TransLogFileInput;
var W3CLoggingCheckBox;
var W3CLogFileInput;
var WebClientDebugCheckBox;
var YP1DebugCheckBox;
var YP2DebugCheckBox;
var SHOUTcastSourceDebugCheckBox;
var UVOX2SourceDebugCheckBox;
var SHOUTcast1ClientDebugCheckBox;
var SHOUTcast2ClientDebugCheckBox;
var RelaySHOUTcastDebugCheckBox;
var RelayUVOXDebugCheckBox;
var RelayDebugCheckBox;
var StreamDataDebugCheckBox;
var HTTPStyleDebugCheckBox;
var StatsDebugCheckBox;
var MicroServerDebugCheckBox;
var ThreadRunnerDebugCheckBox;
var RTMPClientDebugCheckBox;
var ShuffleDebugCheckBox;
var SHOUTcastDebugCheckBox;
var UVOXDebugCheckBox;
var GainDebugCheckBox;
var PlaylistDebugCheckBox;
var MP3EncDebugCheckBox;
var MP3DecDebugCheckBox;
var ResamplerDebugCheckBox;
var RGCalcDebugCheckBox;
var APIDebugCheckBox;
var CalendarDebugCheckBox;
var CaptureDebugCheckBox;
var DJDebugCheckBox;
var FlashPolicyServerDebugCheckBox;
var FileConverterDebugCheckBox;
var SourceRelayDebugCheckBox;
var SourceAndEndpointManagerDebugCheckBox;
var StreamTitleInput;
var StreamURLInput;
var StreamGenreInput;
var AIMInput;
var IRCInput;
var ICQInput;
var UseMetadataCheckBox;
var MetadataPatternInput;
var DisplayMetadataPatternInput;
var TitleFormatInput;
var URLFormatInput;
var DNASPublicSelect;
var TransPublicCheckBox;
var MetaIntervalInput;
var YPAddrInput;
var YPPortInput;
var YPPathInput;
var YPTimeoutInput;
var YPMaxRetriesInput;
var YPReportIntervalInput;
var YPMinReportIntervalInput;
var PortBaseInput;
var DNASIPInput;
var RobotsTxtInput;
var ServerIPInput;
var SourceBindAddressInput;
var DestinationBindAddressInput;
var PasswordInput;
var AdminPasswordInput;
var VUImageDirectoryInput;
var VUImageSuffixInput;
var VUImageMimeTypeInput;
var DNASFlashPolicyServerPortInput;
var DNASFlashPolicyFileInput;
var FlashPolicyServerPortInput;
var FlashPolicyFileInput;
var TransAdminPortInput;
var MP3UnlockKeyNameInput;
var MP3UnlockKeyCodeInput;
var DefaultAuthHashInput;
var BanFileInput;
var RipFileInput;
var RipOnlyCheckBox;
var MaxListenersInput;
var ListenerTimeInput;
var AutoDumpUsersCheckBox;
var CalendarFileInput;
var CalendarEnableRewriteCheckBox;
var NumOfCalendarEventsHidden;
var CalendarEventDeleteButtonArray = new Array(NumOfCalendarEvents);
var CalendarEventTypeSelectArray = new Array(NumOfCalendarEvents);
var CalendarDJNameInputArray = new Array(NumOfCalendarEvents);
var CalendarDJArchiveSelectArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistNameInputArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistLoopAtEndCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistShuffleSelectArray = new Array(NumOfCalendarEvents);
var CalendarPlaylistPriorityInputArray = new Array(NumOfCalendarEvents);
var CalendarRelayURLInputArray = new Array(NumOfCalendarEvents);
var CalendarRelayPriorityInputArray = new Array(NumOfCalendarEvents);
var CalendarPerCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarSunCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarMonCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarTueCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarWedCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarThuCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarFriCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarSatCheckBoxArray = new Array(NumOfCalendarEvents);
var CalendarStartDateInputArray = new Array(NumOfCalendarEvents);
var CalendarEndDateInputArray = new Array(NumOfCalendarEvents);
var CalendarStartTimeInputArray = new Array(NumOfCalendarEvents);
var CalendarDurationInputArray = new Array(NumOfCalendarEvents);
var CalendarTimeOffsetInputArray = new Array(NumOfCalendarEvents);
var CalendarEventAddButton;
var PlaylistFileInput;
var EnableShuffleCheckBox;
var XFadeTimeInput;
var XFadeThresholdInput;
var PlaylistFolderInput;
var PlaylistArchiveFolderInput;
var DJPortInput;
var DJPort2Input;
var DJCipherInput;
var DJAutoDumpSourceTimeInput;
var DJCaptureEnableCheckBox;
var DJBroadcastsPathInput;
var DJFilePatternInput;
var NumOfDJsHidden;
var DJLoginInputArray=new Array(NumOfDJs);
var DJPasswordInputArray=new Array(NumOfDJs);
var DJPriorityInputArray=new Array(NumOfDJs);
var DJCalendarAddButtonArray=new Array(NumOfDJs);
var DJDeleteButtonArray=new Array(NumOfDJs);
var DJAddButton;
var MultiDJSpan;
var EnableCaptureCheckBox;
var CaptureDeviceInput;
var CaptureInputInput;
var CaptureSampleRateInput;
var CaptureNumChannelsInput;
var ApplyReplayGainCheckBox;
var DefaultReplayGainInput;
var DJReplayGainInput;
var CaptureReplayGainInput;
var CalculateReplayGainCheckBox;
var ReplayGainTempFolderInput;
var ReplayGainRunAheadInput;
var ReplayGainDontWriteCheckBox;
var EnhanceReplayGainInput;
var AIMInputBlock;
var IRCInputBlock;
var ICQInputBlock;
var NumOfEndPointsInput;
var DefaultAuthHashTR;
var NumOfEndPointsTR;
var EndPointPathInputArray=new Array(NumOfEndPoints);
var EndPointTypeSelectArray=new Array(NumOfEndPoints);
var EndPointMP3QualitySelectArray=new Array(NumOfEndPoints);
var EndPointBitrateInputArray=new Array(NumOfEndPoints);
var EndPointSamplerateInputArray=new Array(NumOfEndPoints);
var EndPointNumchnsInputArray=new Array(NumOfEndPoints);
var EndPointAuthHashInputArray=new Array(NumOfEndPoints);
var EndPointPasswordInputArray=new Array(NumOfEndPoints);
var EndPointAdminPasswordInputArray=new Array(NumOfEndPoints);
var EndPointTitleInputArray=new Array(NumOfEndPoints);
var EndPointNameInputArray=new Array(NumOfEndPoints);
var EndPointMaxUserInputArray = new Array(NumOfEndPoints);
var MultiPointSpan;
var MultiCalendarSpan;
var DNASConfigreWriteCheckBox;
var TransConfigreWriteCheckBox;
var DNASAdminPageThemeInput;
var DNASAdminPageFavIconInput;
var DNASAdminPageFavIconMimeTypeInput;
var HideStatsCheckBox;
var DNASIntroFileInput;
var DNASBackupFileInput;
var MaxSpecialFileSizeInput;
var ServerBackupFileInput;
var ServerIntroFileInput;
var SongHistoryInput;
var ResetButton;
var DownloadSCServButton;
var DownloadSCTransButton;
var DownloadCalendarButton;
var ConfigurationScrollTable;
var GenerateMinimalCheckBox;
var TranscoderInheritCheckBox;
var BuilderViewModeHidden;
var BuilderViewModeSimpleRadio;
var BuilderViewModeAdvancedRadio;
var DNASDebugModeHidden;
var DNASDebugModeNoneRadio;
var DNASDebugModeAllRadio;
var DNASDebugModeCustomRadio;
var DNASDebugTable;
var TransDebugModeHidden;
var TransDebugModeNoneRadio;
var TransDebugModeAllRadio;
var TransDebugModeCustomRadio;
var TransDebugTable;
var VUImagesHeader;
var VUImagesTable;
var YPHeader;
var YPTable;
var YPTableBlock;
var FlashPolicyHeader;
var FlashPolicyTable;
var MiscellaneousHeader;
var MiscellaneousTable;
var DNASAdminThemeHeader;
var DNASAdminThemeTable;
var LiveCaptureHeader;
var LiveCaptureTable;
var ReplayGainHeader;
var ReplayGainTable;
var CalendarHeader;
var CalendarTable;
var DJsHeader;
var DJsTable;
var DJPortsBlock;
var PlaylistsBlock;
var MetadataPatternBlock;
var DJCipherInputBlock;
var BindAddressBlock;
var DNASConfigreWriteBlock;
var TransConfigreWriteBlock;
var IntroBackupHeader;
var IntroBackupTable;
var CapatureDeviceBlock;

function MarryBasePathAndFile(BP,FN) {
	var t=new String(BP);
	var u=new String(FN);
	if(u.indexOf(":")>-1 || u.indexOf("%")>-1)return u;
	if(u.length>0) {
		if(u[0]==CPD && Platform!=1)return u;
	}
	var a=t.split(CPD);
	while(a[a.length-1]=="") {
		a.pop();
	}
	if(a.length<1)return u;
	var b=u.split(CPD);
	for(var i=0;i<b.length;i++) {
		if(b[i]==".") {
			b.shift();
			i--;
			continue;
		}
		if(b[i]=="..") {
			b.shift();
			a.pop();
			i--;
			continue;
		}
		a.push(b.shift());i--;
	}
	t=a.join(CPD);
	return t;
}

function DoPlatformSelectUpdate(ps) {
	switch(ps) {
		case 0://linux
			CPD=linuxPD;
		break;
		case 2://macintosh
			CPD=macintoshPD;
		break;
		default:
			CPD=windowsPD;
	}
	PushUpdateKill();
	RetoolPathDelimiters(DNASBasePathInput,CPD);
	RetoolPathDelimiters(TranscoderBasePathInput,CPD);
	RetoolPathDelimiters(DNASConfFileInput,CPD);
	RetoolPathDelimiters(TransConfFileInput,CPD);
	RetoolPathDelimiters(DNASLogFileInput,CPD);
	RetoolPathDelimiters(TransLogFileInput,CPD);
	RetoolPathDelimiters(W3CLogFileInput,CPD);
	RetoolPathDelimiters(VUImageDirectoryInput,CPD);
	RetoolPathDelimiters(DNASFlashPolicyFileInput,CPD);
	RetoolPathDelimiters(FlashPolicyFileInput,CPD);
	RetoolPathDelimiters(BanFileInput,CPD);
	RetoolPathDelimiters(RipFileInput,CPD);
	RetoolPathDelimiters(CalendarFileInput,CPD);
	RetoolPathDelimiters(PlaylistFileInput,CPD);
	RetoolPathDelimiters(PlaylistFolderInput,CPD);
	RetoolPathDelimiters(PlaylistArchiveFolderInput,CPD);
	RetoolPathDelimiters(DJBroadcastsPathInput,CPD);
	RetoolPathDelimiters(ReplayGainTempFolderInput,CPD);
	RetoolPathDelimiters(DNASIntroFileInput,CPD);
	RetoolPathDelimiters(DNASBackupFileInput,CPD);
	RetoolPathDelimiters(ServerBackupFileInput,CPD);
	RetoolPathDelimiters(ServerIntroFileInput,CPD);
	for(var i=0;i<NumOfPlaylists;i++) {
		RetoolPathDelimiters(PlaylistMultiFileInputArray[i],CPD);
	}
	PopUpdateKill();
}

function DoConfigSelectUpdate(m) {
	switch(m) {
		case 0:
		DoObjShowHide(1, SCServLinesTextArea);
		DoObjShowHide(0, SCTransLinesTextArea);
		DoObjShowHide(0, SCCalendarLinesTextArea);
		break;
		case 1:
		DoObjShowHide(0, SCServLinesTextArea);
		DoObjShowHide(1, SCTransLinesTextArea);
		DoObjShowHide(0, SCCalendarLinesTextArea);
		break;
		case 2:
		DoObjShowHide(0, SCServLinesTextArea);
		DoObjShowHide(0, SCTransLinesTextArea);
		DoObjShowHide(1, SCCalendarLinesTextArea);
		break;
	}
}

function DoObjShowHide(Show, Obj) {
	if(Show) {
		Obj.style.visibility="";
		Obj.style.display="";
	} else {
		Obj.style.visibility="hidden";
		Obj.style.display="none";
	}
}

function DoBufferTypeUpdate(Type) {
	switch(Type) {
		case 0:
			DoObjShowHide(0, AdaptiveBufferSizeTR);
			DoObjShowHide(1, FixedBufferSizeTR);
		break;
		case 1:
			DoObjShowHide(1, AdaptiveBufferSizeTR);
			DoObjShowHide(0, FixedBufferSizeTR);
		break;
	}
}

function onBufferTypeSelectChanged() {
	BufferType=(BufferTypeSelect.value)*1;
	SetObjCookie(BufferTypeSelect);
	DoBufferTypeUpdate(BufferType);
	DoUpdate();
}

function onAdaptiveBufferSizeInputChanged() {
	AdaptiveBufferSize=(AdaptiveBufferSizeInput.value)*1;
	SetObjCookie(AdaptiveBufferSizeInput);
	DoUpdate();
}

function onFixedBufferSizeInputChanged() {
	FixedBufferSize=(FixedBufferSizeInput.value)*1;
	SetObjCookie(FixedBufferSizeInput);
	DoUpdate();
}

function onBufferHardLimitInputChanged() {
	BufferHardLimit=(BufferHardLimitInput.value)*1;
	SetObjCookie(BufferHardLimitInput);
	DoUpdate();
}

function onMaxHeaderLineSizeInputChanged() {
	MaxHeaderLineSize=(MaxHeaderLineSizeInput.value)*1;
	SetObjCookie(MaxHeaderLineSizeInput);
	DoUpdate();
}

function onMaxHeaderLineCountInputChanged() {
	MaxHeaderLineCount=(MaxHeaderLineCountInput.value)*1;
	SetObjCookie(MaxHeaderLineCountInput);
	DoUpdate();
}

function onNameLookupsCheckBoxClicked() {
	NameLookups=NameLookupsCheckBox.checked;
	SetObjCookie(NameLookupsCheckBox);
	DoUpdate();
}

function onPlatformSelectChanged() {
	Platform=(PlatformSelect.value)*1;
	SetObjCookie(PlatformSelect);
	DoPlatformSelectUpdate(Platform)
	DoUpdate();
}

function onConfigSelectChanged() {
	Config=(ConfigSelect.value)*1;
	SetObjCookie(ConfigSelect);
	DoConfigSelectUpdate(Config)
	DoUpdate();
}

function onDNASBasePathInputChanged() {
	DNASBasePath=DNASBasePathInput.value;
	SetObjCookie(DNASBasePathInput);
	DoUpdate();
}

function onTranscoderBasePathInputChanged() {
	TranscoderBasePath=TranscoderBasePathInput.value;
	SetObjCookie(TranscoderBasePathInput);
	DoUpdate();
}

function onDNASConfFileInputChanged() {
	DNASConfFile=(DNASConfFileInput.value);
	SetObjCookie(DNASConfFileInput);
	DoUpdate();
}

function onTransConfFileInputChanged() {
	TransConfFile=(TransConfFileInput.value);
	SetObjCookie(TransConfFileInput);
	DoUpdate();
}

/*function DoModeSelectUpdate(m) {
	switch(m) {
		case 1:
			NumOfEndPoints=1;
			NumOfEndPointsInput.onchange=null;
			DoObjShowHide(0, NumOfEndPointsTR);
			DoObjShowHide(0, DefaultAuthHashTR);
			DoObjShowHide(1, AIMInputBlock);
			DoObjShowHide(1, IRCInputBlock);
			DoObjShowHide(1, ICQInputBlock);
			DoObjShowHide(1, YPTableBlock);
		break;
		default:
			NumOfEndPointsInput.onchange=null;
			NumOfEndPointsInput.value=NumOfEndPoints;
			NumOfEndPoints=GetObjCookie(NumOfEndPointsInput);
			NumOfEndPointsInput.onchange=onNumOfEndPointsInputChanged;
			DoObjShowHide(1, NumOfEndPointsTR);
			DoObjShowHide(1, DefaultAuthHashTR);
			DoObjShowHide(0, AIMInputBlock);
			DoObjShowHide(0, IRCInputBlock);
			DoObjShowHide(0, ICQInputBlock);
			DoObjShowHide(0, YPTableBlock);
			NumOfEndPoints=(NumOfEndPointsInput.value)*1;
	}
}

function onModeSelectChanged() {
	var m=Mode;
	Mode=(ModeSelect.value)*1;
	SetObjCookie(ModeSelect);
	if(m!=Mode) {
		DoModeSelectUpdate(Mode);
		MultiPointSpanUpdate(NumOfEndPoints);
		DoUpdate();
	}
}*/

function onEnableLoggingCheckBoxClicked() {
	EnableLogging=EnableLoggingCheckBox.checked;
	SetObjCookie(EnableLoggingCheckBox);
	DoUpdate();
}

function onScreenlogCheckBoxClicked() {
	Screenlog=ScreenlogCheckBox.checked;
	SetObjCookie(ScreenlogCheckBox);
	DoUpdate();
}

function onClientConnectLogCheckBoxClicked() {
	ClientConnectLog=ClientConnectLogCheckBox.checked;
	SetObjCookie(ClientConnectLogCheckBox);
	DoUpdate();
}

function onDNASLogFileInputChanged() {
	DNASLogFile=DNASLogFileInput.value;
	SetObjCookie(DNASLogFileInput);
	DoUpdate();
}

function onTransLogFileInputChanged() {
	TransLogFile=TransLogFileInput.value;
	SetObjCookie(TransLogFileInput);
	DoUpdate();
}

function onW3CLoggingCheckBoxClicked() {
	W3CLogging=W3CLoggingCheckBox.checked;
	SetObjCookie(W3CLoggingCheckBox);
	DoUpdate();
}

function onW3CLogFileInputChanged() {
	W3CLogFile=W3CLogFileInput.value;
	SetObjCookie(W3CLogFileInput);
	DoUpdate();
}

function onWebClientDebugCheckBoxClicked() {
	WebClientDebug=(WebClientDebugCheckBox.checked);
	SetObjCookie(WebClientDebugCheckBox);
	DoUpdate();
}

function onYP1DebugCheckBoxClicked() {
	YP1Debug=YP1DebugCheckBox.checked;
	SetObjCookie(YP1DebugCheckBox);
	DoUpdate();
}

function onYP2DebugCheckBoxClicked() {
	YP2Debug=YP2DebugCheckBox.checked;
	SetObjCookie(YP2DebugCheckBox);
	DoUpdate();
}

function onSHOUTcastSourceDebugCheckBoxClicked() {
	SHOUTcastSourceDebug=SHOUTcastSourceDebugCheckBox.checked;
	SetObjCookie(SHOUTcastSourceDebugCheckBox);
	DoUpdate();
}

function onUVOX2SourceDebugCheckBoxClicked() {
	UVOX2SourceDebug=UVOX2SourceDebugCheckBox.checked;
	SetObjCookie(UVOX2SourceDebugCheckBox);
	DoUpdate();
}

function onSHOUTcast1ClientDebugCheckBoxClicked() {
	SHOUTcast1ClientDebug=SHOUTcast1ClientDebugCheckBox.checked;
	SetObjCookie(SHOUTcast1ClientDebugCheckBox);
	DoUpdate();
}

function onSHOUTcast2ClientDebugCheckBoxClicked() {
	SHOUTcast2ClientDebug=SHOUTcast2ClientDebugCheckBox.checked;
	SetObjCookie(SHOUTcast2ClientDebugCheckBox);
	DoUpdate();
}

function onRelaySHOUTcastDebugCheckBoxClicked() {
	RelaySHOUTcastDebug=RelaySHOUTcastDebugCheckBox.checked;
	SetObjCookie(RelaySHOUTcastDebugCheckBox);
	DoUpdate();
}

function onRelayUVOXDebugCheckBoxClicked() {
	RelayUVOXDebug=RelayUVOXDebugCheckBox.checked;
	SetObjCookie(RelayUVOXDebugCheckBox);
	DoUpdate();
}

function onRelayDebugCheckBoxClicked() {
	RelayDebug=RelayDebugCheckBox.checked;
	SetObjCookie(RelayDebugCheckBox);
	DoUpdate();
}

function onStreamDataDebugCheckBoxClicked() {
	StreamDataDebug=StreamDataDebugCheckBox.checked;
	SetObjCookie(StreamDataDebugCheckBox);
	DoUpdate();
}

function onHTTPStyleDebugCheckBoxClicked() {
	HTTPStyleDebug=HTTPStyleDebugCheckBox.checked;
	SetObjCookie(HTTPStyleDebugCheckBox);
	DoUpdate();
}

function onStatsDebugCheckBoxClicked() {
	StatsDebug=StatsDebugCheckBox.checked;
	SetObjCookie(StatsDebugCheckBox);
	DoUpdate();
}

function onMicroServerDebugCheckBoxClicked() {
	MicroServerDebug=MicroServerDebugCheckBox.checked;
	SetObjCookie(MicroServerDebugCheckBox);
	DoUpdate();
}

function onThreadRunnerDebugCheckBoxClicked() {
	ThreadRunnerDebug=ThreadRunnerDebugCheckBox.checked;
	SetObjCookie(ThreadRunnerDebugCheckBox);
	DoUpdate();
}

function onRTMPClientDebugCheckBoxClicked() {
	RTMPClientDebug=RTMPClientDebugCheckBox.checked;
	SetObjCookie(RTMPClientDebugCheckBox);
	DoUpdate();
}

function DoDNASDebugModeUpdate(m) {

	switch(m) {
		case 0:
		case 1:
		DoObjShowHide(0, DNASDebugTable);
		break;
		default:
		DoObjShowHide(1, DNASDebugTable);
		break;
	}
	switch(m) {
		case 0:
		DNASDebugModeNoneRadio.checked=true;
		break;
		case 1:
		DNASDebugModeAllRadio.checked=true;
		break;
		case 2:
		DNASDebugModeCustomRadio.checked=true;
		break;
	}
}

function onDNASDebugModeHiddenChanged() {
	var m=(DNASDebugModeHidden.value)*1;
	if(DNASDebugMode!=m) {
		DNASDebugMode=m;
		SetObjCookie(DNASDebugModeHidden);
		DoDNASDebugModeUpdate(m);
		DoUpdate();
	}
}

function onDNASDebugModeNoneRadioClicked() {
	DNASDebugModeHidden.value=(DNASDebugModeNoneRadio.value)*1;
	CallObjectChanged(DNASDebugModeHidden);
}

function onDNASDebugModeAllRadioClicked() {
	DNASDebugModeHidden.value=(DNASDebugModeAllRadio.value)*1;
	CallObjectChanged(DNASDebugModeHidden);
}

function onDNASDebugModeCustomRadioClicked() {
	DNASDebugModeHidden.value=(DNASDebugModeCustomRadio.value)*1;
	CallObjectChanged(DNASDebugModeHidden);
}

function DoTransDebugModeUpdate(m) {

	switch(m) {
		case 0:
		case 1:
		DoObjShowHide(0, TransDebugTable);
		break;
		default:
		DoObjShowHide(1, TransDebugTable);
		break;
	}
	switch(m) {
		case 0:
		TransDebugModeNoneRadio.checked=true;
		break;
		case 1:
		TransDebugModeAllRadio.checked=true;
		break;
		case 2:
		TransDebugModeCustomRadio.checked=true;
		break;
	}
}

function onTransDebugModeHiddenChanged() {
	var m=(TransDebugModeHidden.value)*1;
	if(TransDebugMode!=m) {
		TransDebugMode=m;
		SetObjCookie(TransDebugModeHidden);
		DoTransDebugModeUpdate(m);
		DoUpdate();
	}
}

function onTransDebugModeNoneRadioClicked() {
	TransDebugModeHidden.value=(TransDebugModeNoneRadio.value)*1;
	CallObjectChanged(TransDebugModeHidden);
}

function onTransDebugModeAllRadioClicked() {
	TransDebugModeHidden.value=(TransDebugModeAllRadio.value)*1;
	CallObjectChanged(TransDebugModeHidden);
}

function onTransDebugModeCustomRadioClicked() {
	TransDebugModeHidden.value=(TransDebugModeCustomRadio.value)*1;
	CallObjectChanged(TransDebugModeHidden);
}

function onShuffleDebugCheckBoxClicked() {
	ShuffleDebug=ShuffleDebugCheckBox.checked;
	SetObjCookie(ShuffleDebugCheckBox);
	DoUpdate();
}

function onSHOUTcastDebugCheckBoxClicked() {
	SHOUTcastDebug=SHOUTcastDebugCheckBox.checked;
	SetObjCookie(SHOUTcastDebugCheckBox);
	DoUpdate();
}

function onUVOXDebugCheckBoxClicked() {
	UVOXDebug=UVOXDebugCheckBox.checked;
	SetObjCookie(UVOXDebugCheckBox);
	DoUpdate();
}

function onGainDebugCheckBoxClicked() {
	GainDebug=GainDebugCheckBox.checked;
	SetObjCookie(GainDebugCheckBox);
	DoUpdate();
}

function onPlaylistDebugCheckBoxClicked() {
	PlaylistDebug=PlaylistDebugCheckBox.checked;
	SetObjCookie(PlaylistDebugCheckBox);
	DoUpdate();
}

function onMP3EncDebugCheckBoxClicked() {
	MP3EncDebug=MP3EncDebugCheckBox.checked;
	SetObjCookie(MP3EncDebugCheckBox);
	DoUpdate();
}

function onMP3DecDebugCheckBoxClicked() {
	MP3DecDebug=MP3DecDebugCheckBox.checked;
	SetObjCookie(MP3DecDebugCheckBox);
	DoUpdate();
}

function onResamplerDebugCheckBoxClicked() {
	ResamplerDebug=ResamplerDebugCheckBox.checked;
	SetObjCookie(ResamplerDebugCheckBox);
	DoUpdate();
}

function onRGCalcDebugCheckBoxClicked() {
	RGCalcDebug=RGCalcDebugCheckBox.checked;
	SetObjCookie(RGCalcDebugCheckBox);
	DoUpdate();
}

function onAPIDebugCheckBoxClicked() {
	APIDebug=APIDebugCheckBox.checked;
	SetObjCookie(APIDebugCheckBox);
	DoUpdate();
}
 
function onCalendarDebugCheckBoxClicked() {
	CalendarDebug=CalendarDebugCheckBox.checked;
	SetObjCookie(CalendarDebugCheckBox);
	DoUpdate();
}

function onCaptureDebugCheckBoxClicked() {
	CaptureDebug=CaptureDebugCheckBox.checked;
	SetObjCookie(CaptureDebugCheckBox);
	DoUpdate();
}
 
function onDJDebugCheckBoxClicked() {
	DJDebug=DJDebugCheckBox.checked;
	SetObjCookie(DJDebugCheckBox);
	DoUpdate();
}

function onFlashPolicyServerDebugCheckBoxClicked() {
	FlashPolicyServerDebug=FlashPolicyServerDebugCheckBox.checked;
	SetObjCookie(FlashPolicyServerDebugCheckBox);
	DoUpdate();
}

function onFileConverterDebugCheckBoxClicked() {
	FileConverterDebug=FileConverterDebugCheckBox.checked;
	SetObjCookie(FileConverterDebugCheckBox);
	DoUpdate();
}

function onSourceRelayDebugCheckBoxClicked() {
	SourceRelayDebug=SourceRelayDebugCheckBox.checked;
	SetObjCookie(SourceRelayDebugCheckBox);
	DoUpdate();
}

function onSourceAndEndpointManagerDebugCheckBoxClicked() {
	SourceAndEndpointManagerDebug=SourceAndEndpointManagerDebugCheckBox.checked;
	SetObjCookie(SourceAndEndpointManagerDebugCheckBox);
	DoUpdate();
}

function onStreamTitleInputChanged() {
	StreamTitle=StreamTitleInput.value;
	SetObjCookie(StreamTitleInput);
	DoUpdate();
}

function onStreamURLInputChanged() {
	StreamURL=StreamURLInput.value;
	SetObjCookie(StreamURLInput);
	DoUpdate();
}

function onStreamGenreDropdownChanged() {
	getSecondaryGenre();
	GetSpecifiedGenre();
	StreamGenreInput.value = StreamGenre;
	SetObjCookie(StreamGenreInput);
	DoUpdate();
}

function onAIMInputChanged() {
	AIM=AIMInput.value;
	SetObjCookie(AIMInput);
	DoUpdate();
}

function onIRCInputChanged() {
	IRC=IRCInput.value;
	SetObjCookie(IRCInput);
	DoUpdate();
}

function onICQInputChanged() {
	ICQ=ICQInput.value;
	SetObjCookie(ICQInput);
	DoUpdate();
}

function onUseMetadataCheckBoxClicked() {
	UseMetadata=UseMetadataCheckBox.checked;
	SetObjCookie(UseMetadataCheckBox);
	DoUpdate();
}

function onMetadataPatternInputChanged() {
	MetadataPattern=MetadataPatternInput.value;
	SetObjCookie(MetadataPatternInput);
	DoUpdate();
}

function onDisplayMetadataPatternInputChanged() {
	DisplayMetadataPattern=DisplayMetadataPatternInput.value;
	SetObjCookie(DisplayMetadataPatternInput);
	DoUpdate();
}

function onTitleFormatInputChanged() {
	TitleFormat=TitleFormatInput.value;
	SetObjCookie(TitleFormatInput);
	DoUpdate();
}

function onURLFormatInputChanged() {
	URLFormat=URLFormatInput.value;
	SetObjCookie(URLFormatInput);
	DoUpdate();
}

function onDNASPublicSelectChanged() {
	DNASPublic=DNASPublicSelect.value;
	SetObjCookie(DNASPublicSelect);
	DoUpdate();
}

function onTransPublicCheckBoxClicked() {
	SCTransPublic=TransPublicCheckBox.checked;
	SetObjCookie(TransPublicCheckBox);
	DoUpdate();
}

function onMetaIntervalInputChanged() {
	MetaInterval=(MetaIntervalInput.value)*1;
	SetObjCookie(MetaIntervalInput);
	DoUpdate();
}

function onYPAddrInputChanged() {
	YPAddr=YPAddrInput.value;
	SetObjCookie(YPAddrInput);
	DoUpdate();
}

function onYPPortInputChanged() {
	YPPort=(YPPortInput.value)*1;
	SetObjCookie(YPPortInput);
	DoUpdate();
}

function onYPPathInputChanged() {
	YPPath=YPPathInput.value;
	SetObjCookie(YPPathInput);
	DoUpdate();
}

function onYPTimeoutInputChanged() {
	YPTimeout=(YPTimeoutInput.value)*1;
	SetObjCookie(YPTimeoutInput);
	DoUpdate();
}

function onYPMaxRetriesInputChanged() {
	YPMaxRetries=(YPMaxRetriesInput.value)*1;
	SetObjCookie(YPMaxRetriesInput);
	DoUpdate();
}

function onYPReportIntervalInputChanged() {
	YPReportInterval=(YPReportIntervalInput.value)*1;
	SetObjCookie(YPReportIntervalInput);
	DoUpdate();
}

function onYPMinReportIntervalInputChanged() {
	YPMinReportInterval=(YPMinReportIntervalInput.value)*1;
	SetObjCookie(YPMinReportIntervalInput);
	DoUpdate();
}

function onPortBaseInputChanged() {
	PortBase=PortBaseInput.value;
	SetObjCookie(PortBaseInput);
	DoUpdate();
}

function onDNASIPInputChanged() {
	DNASIP=DNASIPInput.value;
	SetObjCookie(DNASIPInput);
	DoUpdate();
}

function onRobotsTxtInputChanged() {
	RobotsTxt=RobotsTxtInput.value;
	SetObjCookie(RobotsTxtInput);
	DoUpdate();
}

function onSourceBindAddressInputChanged() {
	SourceBindAddress=SourceBindAddressInput.value;
	SetObjCookie(SourceBindAddressInput);
	DoUpdate();
}

function onDestinationBindAddressInputChanged() {
	DestinationBindAddress=DestinationBindAddressInput.value;
	SetObjCookie(DestinationBindAddressInput);
	DoUpdate();
}

function onTransAdminPortInputChanged() {
	TransAdminPort=(TransAdminPortInput.value)*1;
	SetObjCookie(TransAdminPortInput);
	DoUpdate();
}

function onPasswordInputChanged() {
	Password=PasswordInput.value;
	SetObjCookie(PasswordInput);
	DoUpdate();
}

function onAdminPasswordInputChanged() {
	AdminPassword=AdminPasswordInput.value;
	SetObjCookie(AdminPasswordInput);
	DoUpdate();
}

function onVUImageDirectoryInputChanged() {
	VUImageDirectory=VUImageDirectoryInput.value;
	SetObjCookie(VUImageDirectoryInput);
	DoUpdate();
}

function onVUImageSuffixInputChanged() {
	VUImageSuffix=VUImageSuffixInput.value;
	SetObjCookie(VUImageSuffixInput);
	DoUpdate();
}

function onVUImageMimeTypeInputChanged() {
	VUImageMimeType=VUImageMimeTypeInput.value;
	SetObjCookie(VUImageMimeTypeInput);
	DoUpdate();
}

function onDNASFlashPolicyFileInputChanged() {
	DNASFlashPolicyFile=DNASFlashPolicyFileInput.value;
	SetObjCookie(DNASFlashPolicyFileInput);
	DoUpdate();
}

function onDNASFlashPolicyServerPortInputChanged() {
	DNASFlashPolicyServerPort=(DNASFlashPolicyServerPortInput.value)*1;
	SetObjCookie(DNASFlashPolicyServerPortInput);
	DoUpdate();
}

function onFlashPolicyFileInputChanged() {
	FlashPolicyFile=FlashPolicyFileInput.value;
	SetObjCookie(FlashPolicyFileInput);
	DoUpdate();
}

function onFlashPolicyServerPortInputChanged() {
	FlashPolicyServerPort=(FlashPolicyServerPortInput.value)*1;
	SetObjCookie(FlashPolicyServerPortInput);
	DoUpdate();
}

function onMP3UnlockKeyNameInputChanged() {
	MP3UnlockKeyName=MP3UnlockKeyNameInput.value;
	SetObjCookie(MP3UnlockKeyNameInput);
	DoUpdate();
}

function onMP3UnlockKeyCodeInputChanged() {
	MP3UnlockKeyCode=MP3UnlockKeyCodeInput.value;
	SetObjCookie(MP3UnlockKeyCodeInput);
	DoUpdate();
}

function onDefaultAuthHashInputChanged() {
	DefaultAuthHash=(DefaultAuthHashInput.value);
	SetObjCookie(DefaultAuthHashInput);
	DoUpdate();
}

function onBanFileInputChanged() {
	BanFile=BanFileInput.value;
	SetObjCookie(BanFileInput);
	DoUpdate();
}

function onRipFileInputChanged() {
	RipFile=RipFileInput.value;
	SetObjCookie(RipFileInput);
	DoUpdate();
}

function onRipOnlyCheckBoxClicked() {
	RipOnly=RipOnlyCheckBox.checked;
	SetObjCookie(RipOnlyCheckBox);
	DoUpdate();
}

function onMaxListenersInputChanged() {
	MaxListeners=MaxListenersInput.value;
	SetObjCookie(MaxListenersInput);
	DoUpdate();
}

function onListenerTimeInputChanged() {
	ListenerTime=(ListenerTimeInput.value)*1;
	SetObjCookie(ListenerTimeInput);
	DoUpdate();
}

function onAutoDumpUsersCheckBoxClicked() {
	AutoDumpUsers=AutoDumpUsersCheckBox.checked;
	SetObjCookie(AutoDumpUsersCheckBox);
	DoUpdate();
}

function onCalendarFileInputChanged() {
	CalendarFile=CalendarFileInput.value;
	SetObjCookie(CalendarFileInput);
	DoUpdate();
}

function onCalendarEnableRewriteCheckBoxClicked() {
	CalendarEnableRewrite=CalendarEnableRewriteCheckBox.checked;
	SetObjCookie(CalendarEnableRewriteCheckBox);
	DoUpdate();
}

function onPlaylistFileInputChanged() {
	PlaylistFile=PlaylistFileInput.value;
	SetObjCookie(PlaylistFileInput);
	DoUpdate();
}
 
function onEnableShuffleCheckBoxClicked() {
	EnableShuffle=EnableShuffleCheckBox.checked;
	SetObjCookie(EnableShuffleCheckBox);
	DoUpdate();
}

function onXFadeTimeInputChanged() {
	XFadeTime=(XFadeTimeInput.value)*1;
	SetObjCookie(XFadeTimeInput);
	DoUpdate();
}

function onXFadeThresholdInputChanged() {
	XFadeThreshold=XFadeThresholdInput.value;
	SetObjCookie(XFadeThresholdInput);
	DoUpdate();
}

function onPlaylistFolderInputChanged() {
	PlaylistFolder=PlaylistFolderInput.value;
	SetObjCookie(PlaylistFolderInput);
	DoUpdate();
}

function onPlaylistArchiveFolderInputChanged() {
	PlaylistArchiveFolder=PlaylistArchiveFolderInput.value;
	SetObjCookie(PlaylistArchiveFolderInput);
	DoUpdate();
}

function onDJPortInputChanged() {
	DJPort=(DJPortInput.value)*1;
	SetObjCookie(DJPortInput);
	DoUpdate();
}

function onDJPort2InputChanged() {
	DJPort2=(DJPort2Input.value)*1;
	SetObjCookie(DJPort2Input);
	DoUpdate();
}

function onDJCipherInputChanged() {
	DJCipher=DJCipherInput.value;
	SetObjCookie(DJCipherInput);
	DoUpdate();
}

function onDJAutoDumpSourceTimeInputChanged() {
	DJAutoDumpSourceTime=(DJAutoDumpSourceTimeInput.value)*1;
	SetObjCookie(DJAutoDumpSourceTimeInput);
	DoUpdate();
}

function onDJCaptureEnableCheckBoxClicked() {
	DJCaptureEnable=DJCaptureEnableCheckBox.checked;
	SetObjCookie(DJCaptureEnableCheckBox);
	DoUpdate();
}

function onDJBroadcastsPathInputChanged() {
	DJBroadcastsPath=DJBroadcastsPathInput.value;
	SetObjCookie(DJBroadcastsPathInput);
	DoUpdate();
}

function onDJFilePatternInputChanged() {
	DJFilePattern=DJFilePatternInput.value;
	SetObjCookie(DJFilePatternInput);
	DoUpdate();
} 

function onNumOfDJsHiddenChanged() {
	NumOfDJs=(NumOfDJsHidden.value)*1;
	SetObjCookie(NumOfDJsHidden);
	MultiDJSpanUpdate();
	DoUpdate();
}

function onDJLoginInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=DJLoginInputArray.indexOf(obj)
	if(i<0)return;
	var OldDJLogin=DJLoginArray[i];
	DJLoginArray[i]=(DJLoginInputArray[i].value);
	SetObjCookie(DJLoginInputArray[i]);
	var NewDJLogin=DJLoginArray[i];
	if(OldDJLogin!=NewDJLogin) {
		for(var i=0;i<NumOfCalendarEvents;i++) {
			if(CalendarDJNameArray[i]==OldDJLogin) {
				CalendarDJNameArray[i]=NewDJLogin;
				SetObjectValue(CalendarDJNameInputArray[i],NewDJLogin);
				SetObjCookie(CalendarDJNameInputArray[i]);
				PushUpdateKill();
				CallObjectChanged(CalendarDJNameInputArray[i]);
				PopUpdateKill();
			}
		}
		DoUpdate();
	}
}

function onDJPasswordInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=DJPasswordInputArray.indexOf(obj)
	if(i<0)return;
	DJPasswordArray[i]=(DJPasswordInputArray[i].value);
	SetObjCookie(DJPasswordInputArray[i]);
	DoUpdate();
}

function onDJPriorityInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=DJPriorityInputArray.indexOf(obj)
	if(i<0)return;
	DJPriorityArray[i]=(DJPriorityInputArray[i].value);
	SetObjCookie(DJPriorityInputArray[i]);
	DoUpdate();
}

function onDJCalendarAddButtonClicked(w)
{
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var dji=DJCalendarAddButtonArray.indexOf(obj);
	var DJLoginObj=DJLoginInputArray[dji];
	if(DJLoginObj==null || DJLoginObj==undefined)return;
	if(DJLoginObj.value=="")return;
	DoAddCalendarEvent();
	DoUpdate();
	var CalEvtTypeObj=CalendarEventTypeSelectArray[NumOfCalendarEvents-1];
	var CalDJNameObj=CalendarDJNameInputArray[NumOfCalendarEvents-1];
	if(CalDJNameObj!=null && CalDJNameObj!=undefined && CalEvtTypeObj!=null && CalEvtTypeObj!=undefined) {
		if(dji>-1)
		{
			PushUpdateKill();
			SetObjectValue(CalEvtTypeObj,"dj");
			CallObjectChanged(CalEvtTypeObj);
			SetObjectValue(CalDJNameObj,DJLoginObj.value);
			CallObjectChanged(CalDJNameObj);
			PopUpdateKill();
			DoUpdate();
			alert("The DJ has been added to the Calendar event list.  Double check the name matches and to set the desired run-time(s).");
		}
	}
}

function onDJDeleteButtonClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	DoDeleteDJ(obj);
	DoUpdate();
}

function onDJAddButtonClicked() {
	// check so that we only add in more djs if the last attempt was ok
	if(!NumOfDJs || DJLoginInputArray[NumOfDJs-1].value) {
		DoAddDJ();
		DoUpdate();
	}
}

function onEnableCaptureCheckBoxClicked() {
	EnableCapture=EnableCaptureCheckBox.checked;
	DoObjShowHide(EnableCapture, CapatureDeviceBlock);
	SetObjCookie(EnableCaptureCheckBox);
	DoUpdate();
}

function onCaptureDeviceInputChanged() {
	CaptureDevice=CaptureDeviceInput.value;
	SetObjCookie(CaptureDeviceInput);
	DoUpdate();
}

function onCaptureInputInputChanged() {
	CaptureInput=CaptureInputInput.value;
	SetObjCookie(CaptureInputInput);
	DoUpdate();
}

function onCaptureSampleRateInputChanged() {
	CaptureSampleRate=CaptureSampleRateInput.value;
	SetObjCookie(CaptureSampleRateInput);
	DoUpdate();
}

function onCaptureNumChannelsInputChanged() {
	CaptureNumChannels=CaptureNumChannelsInput.value;
	SetObjCookie(CaptureNumChannelsInput);
	DoUpdate();
}

function onApplyReplayGainCheckBoxClicked() {
	ApplyReplayGain=ApplyReplayGainCheckBox.checked;
	SetObjCookie(ApplyReplayGainCheckBox);
	DoUpdate();
}

function onDefaultReplayGainInputChanged() {
	DefaultReplayGain=DefaultReplayGainInput.value;
	SetObjCookie(DefaultReplayGainInput);
	DoUpdate();
}

function onDJReplayGainInputChanged() {
	DJReplayGain=DJReplayGainInput.value;
	SetObjCookie(DJReplayGainInput);
	DoUpdate();
}

function onCaptureReplayGainInputChanged() {
	CaptureReplayGain=CaptureReplayGainInput.value;
	SetObjCookie(CaptureReplayGainInput);
	DoUpdate();
}

function onCalculateReplayGainCheckBoxClicked() {
	CalculateReplayGain=CalculateReplayGainCheckBox.checked;
	SetObjCookie(CalculateReplayGainCheckBox);
	DoUpdate();
}

function onReplayGainTempFolderInputChanged() {
	ReplayGainTempFolder=ReplayGainTempFolderInput.value;
	SetObjCookie(ReplayGainTempFolderInput);
	DoUpdate();
}

function onReplayGainRunAheadInputChanged() {
	ReplayGainRunAhead=ReplayGainRunAheadInput.value;
	SetObjCookie(ReplayGainRunAheadInput);
	DoUpdate();
}

function onReplayGainDontWriteCheckBoxClicked() {
	ReplayGainDontWrite=ReplayGainDontWriteCheckBox.checked;
	SetObjCookie(ReplayGainDontWriteCheckBox);
	DoUpdate();
}

function onEnhanceReplayGainInputChanged() {
	EnhanceReplayGain=EnhanceReplayGainInput.value;
	SetObjCookie(EnhanceReplayGainInput);
	DoUpdate();
}

function onNumOfEndPointsInputChanged() {
	var w=(NumOfEndPoints)*1;
	NumOfEndPoints=(NumOfEndPointsInput.value)*1;
	if(NumOfEndPoints<1){
		NumOfEndPointsInput.value = 1;
		NumOfEndPoints = 1;
	}
	SetObjCookie(NumOfEndPointsInput);
	if(w!=NumOfEndPoints) {
		MultiPointSpanUpdate(NumOfEndPoints);
		DoUpdate();
	}
}

function DoEndPointTypeSelectUpdate(obj) {
	var i=EndPointTypeSelectArray.indexOf(obj);
	var y=$("EndPoint"+(i+1)+"MP3FormatSpan");
	if(obj.value!="mp3") {
		DoObjShowHide(0, y);
	} else {
		DoObjShowHide(1, y);
	}
}

function onEndPointTypeSelectChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	SetObjCookie(obj);
	DoEndPointTypeSelectUpdate(obj);
	DoUpdate();
}

function onEndPointGenericObjectChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	SetObjCookie(obj);
	DoUpdate();
}

function onDNASConfigreWriteCheckBoxClicked() {
	DNASConfigreWrite=DNASConfigreWriteCheckBox.checked;
	SetObjCookie(DNASConfigreWriteCheckBox);
	DoUpdate();
}

function onTransConfigreWriteCheckBoxClicked() {
	TransConfigreWrite=TransConfigreWriteCheckBox.checked;
	SetObjCookie(TransConfigreWriteCheckBox);
	DoUpdate();
}

function onDNASAdminPageThemeInputChanged() {
	DNASAdminPageTheme=DNASAdminPageThemeInput.value;
	SetObjCookie(DNASAdminPageThemeInput);
	DoUpdate();
}

function onDNASAdminPageFavIconInputChanged() {
	DNASAdminPageFavIcon=DNASAdminPageFavIconInput.value;
	SetObjCookie(DNASAdminPageFavIconInput);
	DoUpdate();
}

function onDNASAdminPageFavIconMimeTypeInputChanged() {
	DNASAdminPageFavIconMimeType=DNASAdminPageFavIconMimeTypeInput.value;
	SetObjCookie(DNASAdminPageFavIconMimeTypeInput);
	DoUpdate();
}

function onHideStatsCheckBoxChanged() {
	HideStats=HideStatsCheckBox.checked;
	SetObjCookie(HideStatsCheckBox);
	DoUpdate();
}

function onDNASIntroFileInputChanged() {
	DNASIntroFile=DNASIntroFileInput.value;
	SetObjCookie(DNASIntroFileInput);
	DoUpdate();
}

function onDNASBackupFileInputChanged() {
	DNASBackupFile=DNASBackupFileInput.value;
	SetObjCookie(DNASBackupFileInput);
	DoUpdate();
}

function onMaxSpecialFileSizeInputChanged() {
	MaxSpecialFileSize=(MaxSpecialFileSizeInput.value)*1;
	SetObjCookie(MaxSpecialFileSizeInput);
	DoUpdate();
}

function onServerBackupFileInputChanged() {
	ServerBackupFile=ServerBackupFileInput.value;
	SetObjCookie(ServerBackupFileInput);
	DoUpdate();
}

function onServerIntroFileInputChanged() {
	ServerIntroFile=ServerIntroFileInput.value;
	SetObjCookie(ServerIntroFileInput);
	DoUpdate();
}

function onSongHistoryInputChanged() {
	SongHistory=SongHistoryInput.value;
	SetObjCookie(SongHistoryInput);
	DoUpdate();
}

function DoAddPlaylist() {
	// check so that we only add in more playlists if the last attempt was ok
	if(!NumOfPlaylists || PlaylistMultiSymNameInputArray[NumOfPlaylists-1].value) {
		NumOfPlaylists++;
		NumOfPlaylistsHidden.value=NumOfPlaylists;
		SetObjCookie(NumOfPlaylistsHidden);
		MultiPlaylistSpanUpdate();
	}
}

function onAddPlaylistButtonClicked() {
	DoAddPlaylist();
}

function onNumOfPlaylistsHiddenChanged() {
	NumOfPlaylists=(NumOfPlaylistsHidden.value)*1;
	SetObjCookie(NumOfPlaylistsHidden);
	MultiPlaylistSpanUpdate();
	DoUpdate();
}

function onResetButtonClicked() {
	for(var i=1;i<=CollapsedArrayNum;i++) {
		if(CollapsedArray[i-1]==1) {
			DoObjShowHide(true, $(i));
		}
	}
	deleteAllCookies();
	DoInit();
}

function onDownloadSCServButtonClicked() {
	$("config").value="fn="+DNASConfFile+"&body="+scscl;
	$("configs").submit();
}

function onDownloadSCTransButtonClicked() {
	$("config").value="fn="+TransConfFile+"&body="+sctcl;
	$("configs").submit();
}

function onDownloadCalendarButtonClicked() {
	$("config").value="fn=calendar.xml&body="+scccl;
	$("configs").submit();
}

function bts(p){
	if(p){
		return "1";
	}
	return "0";
}

var NL;
function OutInt(name, p, def) {
	if(GenerateMinimal && p == def) return "";
	return name+"="+bts(p)+NL;
}

function OutIntRaw(name, p, def) {
	if(GenerateMinimal && p == def) return "";
	return name+"="+p+NL;
}

function OutStr(name, p, def) {
	if(GenerateMinimal && p == def) return "";
	return name+"="+p+NL;
}

function OutServIndex(rindex) {
	return ((NumOfEndPoints >= 2) ? "_"+rindex : "")+"=";
}

function OutTransIndex(rindex, uscore) {
	return ((NumOfEndPoints >= 2) ? (uscore?"_":"")+(rindex+1) : "")+(uscore?"=":"");
}

function OutTransStr(name, rindex, p, def) {
	if(GenerateMinimal && p == def) return "";
	return name+OutServIndex(rindex)+p+NL;
}

function DoUpdate() {
	if(KillDoUpdate)return;
	PushUpdateKill();

	scssl="";
	sctsl="";

	var DNASBinary="";
	var TransBinary="";
	var scsbps="";
	var sctbps="";
	switch(Platform*1) {
		case 0:
			NL=linux;
		break;
		case 2:
			NL=macintosh;
		break;
		default:
			NL=windows;
	}

	switch(Platform*1) {
		case 0:
		case 2:
			scssfn="start_sc_serv.sh";
			sctsfn="start_sc_trans.sh";
			scssl+="# DNAS startup shell script built with the SHOUTcast 2 Configuration Builder"+NL;
			sctsl+="# Transcoder startup shell script built with the SHOUTcast 2 Configuration Builder"+NL;
			DNASBinary="sc_serv";
			TransBinary="sc_trans";
			if(DNASBasePath.length>0) {
				if(DNASBasePath[0]!=CPD) {
					scsbps="."+CPD;
				}
			} else {
					scsbps="."+CPD;
			}
			if(TranscoderBasePath.length>0) {
				if(TranscoderBasePath[0]!=CPD) {
					sctbps="."+CPD;
				}
			} else {
				sctbps="."+CPD;
			}
		break;
		default:
			scssfn="start_sc_serv.bat";
			sctsfn="start_sc_trans.bat";
			scssl+="REM DNAS startup batch file built with the SHOUTcast 2 Configuration Builder"+NL;
			sctsl+="REM Transcoder startup batch file built with the SHOUTcast 2 Configuration Builder"+NL;
			DNASBinary="sc_serv.exe";
			TransBinary="sc_trans.exe";
	}

	scscl="";//";DNAS Configuration File"+NL+";Made with the SHOUTcast 2 Configuration Builder"+NL+NL;
	sctcl="";//";Transcoder Configuration File"+NL+";Made with the SHOUTcast 2 Configuration Builder"+NL+NL;
	scccl="<?xml version=\"1.0\" encoding=\"UTF-8\"?>"+NL/*+"<!--Transcoder Events Configuration File-->"+NL+"<!--Made with the SHOUTcast 2 Configuration Builder-->"+NL*/;

	scssl+="\""+scsbps+MarryBasePathAndFile(DNASBasePath,DNASBinary)+"\" \""+scsbps+MarryBasePathAndFile(DNASBasePath,DNASConfFile)+"\""+NL;
	sctsl+="\""+sctbps+MarryBasePathAndFile(TranscoderBasePath,TransBinary)+"\" \""+sctbps+MarryBasePathAndFile(TranscoderBasePath,TransConfFile)+"\""+NL;

	if(BuilderViewMode==1) {
		// make the tools use minimal re-write as applicable
		if(GenerateMinimal && DNASConfigreWrite)
			scscl+=OutIntRaw("configrewrite",2,0);
		else
			scscl+=OutInt("configrewrite",DNASConfigreWrite,0);

		if(GenerateMinimal && TransConfigreWrite)
			sctcl+=OutIntRaw("configrewrite",2,0);
		else
			sctcl+=OutInt("configrewrite",TransConfigreWrite,0);
	}
	scccl+="<eventlist>"+NL;

	var TransDebug = (TransDebugMode != 0);
	if(TransDebug) {
		sctcl+=OutInt("shuffledebug",ShuffleDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("shoutcastdebug",SHOUTcastDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("uvoxdebug",UVOXDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("gaindebug",GainDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("playlistdebug",PlaylistDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("mp3encdebug",MP3EncDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("mp3decdebug",MP3DecDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("resamplerdebug",ResamplerDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("rgcalcdebug",RGCalcDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("apidebug",APIDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("calendardebug",CalendarDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("capturedebug",CaptureDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("djdebug",DJDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("flashpolicyserverdebug",FlashPolicyServerDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("fileconverterdebug",FileConverterDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("sourcerelaydebug",SourceRelayDebug && TransDebug || TransDebugMode==1,0);
		sctcl+=OutInt("sourceandendpointmanagerdebug",SourceAndEndpointManagerDebug && TransDebug || TransDebugMode==1,0);
	}

	sctcl+="streamtitle="+StreamTitle+NL;
	sctcl+="streamurl="+StreamURL+NL;
	if(StreamGenre != null && StreamGenre != "") {
		sctcl+="genre="+StreamGenre+NL;
	}
	/*if(Mode==1 && AIM!="")sctcl+="aim="+AIM+NL;
	if(Mode==1 && IRC!="")sctcl+="irc="+IRC+NL;
	if(Mode==1 && ICQ!="")sctcl+="icq="+ICQ+NL;*/
	sctcl+=OutInt("usemetadata",UseMetadata,1);
	if(BuilderViewMode==1 && MetadataPattern!="")sctcl+="metadatapattern="+MetadataPattern+NL;
	if(BuilderViewMode==1 && DisplayMetadataPattern!="")sctcl+="displaymetadatapattern="+DisplayMetadataPattern+NL;
	if(TransAdminPort>0 && TransAdminPort<65536) {
		sctcl+="adminport="+TransAdminPort+NL;
		sctcl+="adminpassword="+AdminPassword+NL;
		sctcl+="adminuser=admin"+NL;
	} else {
		if(!GenerateMinimal) {
			sctcl+="adminport=0"+NL;
		}
	}

	if(BuilderViewMode==1) {
		if(VUImageDirectory!="" && VUImageDirectory!="vuimages\\" && VUImageDirectory!="vuimages/") {
			sctcl+="vuimagedirectory="+MarryBasePathAndFile(TranscoderBasePath,VUImageDirectory)+NL;
		}
		sctcl+=OutStr("vuimagesuffix",VUImageSuffix,"png");
		if(VUImageMimeType!="") {
			sctcl+=OutStr("vuimagemimetype",VUImageMimeType,"image/png");
		}

		if(FlashPolicyFile!="") {
			sctcl+=OutStr("flashpolicyfile",MarryBasePathAndFile(TranscoderBasePath,FlashPolicyFile),"crossdomain.xml");
		}
		if(FlashPolicyServerPort>0 && FlashPolicyServerPort<65536 && FlashPolicyServerPort!=NaN && FlashPolicyServerPort!=null) {
			sctcl+="flashpolicyserverport="+FlashPolicyServerPort+NL;
		} else {
			if(!GenerateMinimal) {
				sctcl+="flashpolicyserverport=0"+NL;
			}
		}
	}

	if(!TranscoderInherit) {
		/*if(Mode==1) {
			sctcl+="password="+Password+NL;
		}*/
	} else {
		sctcl+="inheritconfig="+MarryBasePathAndFile(DNASBasePath,DNASConfFile)+NL;
	}
	if(MP3UnlockKeyName!="") {
		sctcl+="unlockkeyname="+MP3UnlockKeyName+NL;
	}
	if(MP3UnlockKeyCode!="") {
		sctcl+="unlockkeycode="+MP3UnlockKeyCode+NL;
	}
	sctcl+=OutInt("log",EnableLogging,1);
	sctcl+=OutInt("screenlog",Screenlog,1);
	sctcl+="logfile="+MarryBasePathAndFile(TranscoderBasePath,TransLogFile)+NL;
	sctcl+=OutInt("public",SCTransPublic,0);

	if(PlaylistFile!="")
	{
		sctcl+="playlistfile="+MarryBasePathAndFile(TranscoderBasePath,PlaylistFile)+NL;
	}
	sctcl+=OutInt("shuffle",EnableShuffle,1);
	if(XFadeTime!=0 && XFadeTime!="" && XFadeTime!=NaN && XFadeTime!=null) {
		sctcl+=OutStr("xfade",XFadeTime,1);
		if(XFadeThreshold!=0 && XFadeThreshold!="" && XFadeThreshold!=NaN && XFadeThreshold!=null) {
			sctcl+=OutStr("xfadethreshold",XFadeThreshold,10);
		} else {
			sctcl+="xfadethreshold=0"+NL;
		}
	} else {
		sctcl+="xfade=0"+NL;
	}

	if(BuilderViewMode==1) {
		if(PlaylistFolder!="") {
			sctcl+="playlists="+MarryBasePathAndFile(TranscoderBasePath,PlaylistFolder)+NL;
		}
		if(PlaylistArchiveFolder!="") {
			sctcl+="archive="+MarryBasePathAndFile(TranscoderBasePath,PlaylistArchiveFolder)+NL;
		}

		if(CalendarFile!="") {
			sctcl+="calendarfile="+MarryBasePathAndFile(TranscoderBasePath,CalendarFile)+NL;
		}
		sctcl+=OutInt("calendarrewrite",CalendarEnableRewrite,1);

		var djp1v=(DJPort>0 && DJPort<65536 && DJPort!=NaN);
		var djp2v=(DJPort2>0 && DJPort2<65536 && DJPort2!=NaN);
		var djpv=(djp1v || djp2v);
		if(djp1v && djp2v && djpv) {
			if(DJPort+1==DJPort2)djpv=false;
		}
		if(djpv) {
			if(djp1v) {
				sctcl+="djport="+DJPort+NL;
			} else {
				if(!GenerateMinimal) {
					sctcl+="djport=0"+NL;
				}
			}
			if(djp2v) {
				sctcl+="djport2="+DJPort2+NL;
			} else {
				if(!GenerateMinimal) {
					sctcl+="djport2=0"+NL;
				}
			}

			sctcl+=OutStr("autodumpsourcetime",DJAutoDumpSourceTime,30);
			sctcl+=OutInt("djcapture",DJCaptureEnable,1);
			if(DJCaptureEnable) {
				sctcl+="djbroadcasts="+MarryBasePathAndFile(TranscoderBasePath,DJBroadcastsPath)+NL;
				if(DJFilePattern!="")sctcl+="djfilepattern="+DJFilePattern+NL;
			}
		} else {
			if(!GenerateMinimal) {
				sctcl+="djport=0"+NL;
				sctcl+="djport2=0"+NL;
			}
		}
	}

	if(BuilderViewMode==1) {
		if(DJCipher!="" && DJCipher!="foobar") sctcl+="djcipher="+DJCipher+NL;

		sctcl+=OutInt("capture",EnableCapture,0);
		if(EnableCapture) {
			if(CaptureDevice!="") sctcl+="capturedevice="+CaptureDevice+NL;
			if(CaptureInput!="") sctcl+="captureinput="+CaptureInput+NL;
			sctcl+=OutStr("capturesamplerate",CaptureSampleRate,44100);
			sctcl+=OutStr("capturechannels",CaptureNumChannels,2);
		}
		sctcl+=OutInt("applyreplaygain",ApplyReplayGain,0);
		if(DefaultReplayGain!="") {
			sctcl+=OutStr("defaultreplaygain",DefaultReplayGain,"0.0");
		}
		if(DJReplayGain!="") {
			sctcl+=OutStr("djreplaygain",DJReplayGain,"0.0");
		}
		if(CaptureReplayGain!="") {
			sctcl+=OutStr("capturereplaygain",CaptureReplayGain,"0.0");
		}
		sctcl+=OutInt("calculatereplaygain",CalculateReplayGain,0);
		if(ReplayGainTempFolder!="") {
			sctcl+="replaygaintmpdir="+MarryBasePathAndFile(TranscoderBasePath,ReplayGainTempFolder)+NL;
		}
		sctcl+=OutStr("replaygainrunahead",ReplayGainRunAhead,2);
		sctcl+=OutInt("replaygaindontwrite",ReplayGainDontWrite,0);
		if(EnhanceReplayGain!="") {
			sctcl+=OutStr("enhancereplaygain",EnhanceReplayGain,"6.0");
		}
	}

	if(BuilderViewMode==1) {
		if(ServerBackupFile!="") {
			sctcl+="serverbackupfile="+MarryBasePathAndFile(TranscoderBasePath,ServerBackupFile)+NL;
		}
		if(ServerIntroFile!="") {
			sctcl+="serverintrofile="+MarryBasePathAndFile(TranscoderBasePath,ServerIntroFile)+NL;
		}
	}

	var sa=0;
	for(var i=0;i<NumOfPlaylists;i++) {
		var pn=PlaylistMultiSymNameArray[i];
		var pf=PlaylistMultiFileArray[i];
		if(pn=="" || pf==""){sa++;continue;}
		sctcl+=NL;
		sctcl+="playlistfilename_"+(i+1-sa)+"="+pn+NL;
		sctcl+="playlistfilepath_"+(i+1-sa)+"="+MarryBasePathAndFile(TranscoderBasePath,pf)+NL;
	}

	var sa=0;
	for(var i=0;i<NumOfDJs;i++) {
		var dl=DJLoginArray[i];
		var dp=DJPasswordArray[i];
		if(dl=="" || dp==""){sa++;continue;}

		sctcl+=NL;
		sctcl+="djlogin_"+(i+1-sa)+"="+dl+NL;
		sctcl+="djpassword_"+(i+1-sa)+"="+dp+NL;
		sctcl+="djpriority_"+(i+1-sa)+"="+(DJPriorityArray[i])*1+NL;
	}

	for(var i=0;i<NumOfCalendarEvents;i++) {
		var cet=CalendarEventTypeArray[i];
		var cdja=CalendarDJArchiveArray[i];
		var cdjn=CalendarDJNameArray[i];
		var cpl=CalendarPlaylistLoopAtEndArray[i];
		var cps=CalendarPlaylistShuffleArray[i];
		var cpp=CalendarPlaylistPriorityArray[i];
		var cpn=CalendarPlaylistNameArray[i];
		var cru=CalendarRelayURLArray[i];
		var crp=CalendarRelayPriorityArray[i];
		if(cet=="dj") {
			if(cdjn=="")continue;
			scccl+="\t<event type=\""+cet+"\">"+NL;
			scccl+="\t\t<dj archive=\""+cdja+"\">"+cdjn+"</dj>"+NL;
		}
		if(cet=="playlist") {
			if(cpn=="")continue;
			if(cpp=="")continue;
			scccl+="\t<event type=\""+cet+"\">"+NL;
			scccl+="\t\t<playlist loopatend=\""+bts(cpl)+"\" shuffle=\""+cps+"\" priority=\""+cpp+"\">"+NL;
			scccl+="\t\t\t"+cpn+NL;
			scccl+="\t\t</playlist>"+NL;
		}
		if(cet=="relay") {
			if(cru=="")continue;
			if(crp=="")continue;
			scccl+="\t<event type=\""+cet+"\">"+NL;
			scccl+="\t\t<relay url=\""+cru+"\" priority=\""+crp+"\"/>"+NL;
		}
		var rep=0;
		if(CalendarPerArray[i]){rep+=128;}
		if(CalendarSunArray[i]){rep+=1;}
		if(CalendarMonArray[i]){rep+=2;}
		if(CalendarTueArray[i]){rep+=4;}
		if(CalendarWedArray[i]){rep+=8;}
		if(CalendarThuArray[i]){rep+=16;}
		if(CalendarFriArray[i]){rep+=32;}
		if(CalendarSatArray[i]){rep+=64;}
		var sd=CalendarStartDateArray[i];
		var ed=CalendarEndDateArray[i];
		var st=CalendarStartTimeArray[i];
		var d=CalendarDurationArray[i];
		var to=CalendarTimeOffsetArray[i];
		var su=false;
		if(rep>0){su=true;rep=""+NL+"\t\t\trepeat=\""+rep+"\""+"\t\t";}else {rep="";}
		if(sd!=""){su=true;sd=""+NL+"\t\t\tstartdate=\""+sd+"\""+"\t\t";}else{sd="";}
		if(ed!=""){su=true;ed=""+NL+"\t\t\tenddate=\""+ed+"\""+"\t\t";}else{ed="";}
		if(st!=""){su=true;st=""+NL+"\t\t\tstarttime=\""+st+"\""+"\t\t";}else{st="";}
		if(d!=""){su=true;d=  ""+NL+"\t\t\tduration=\""+d+"\""+"\t\t";}else{d="";}
		if(to!=""){su=true;to=""+NL+"\t\t\ttimeoffset=\""+to+"\""+"\t\t";}else{to="";}
		if(su){su=NL+"\t\t";}else{su=" ";}
		scccl+="\t\t<calendar"+rep+sd+ed+st+d+to+su+"/>"+NL;
		scccl+="\t</event>"+NL;
	}

	var DNASDebug = (DNASDebugMode != 0);
	if(DNASDebug) {
		scscl+=OutInt("yp1debug",YP1Debug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("yp2debug",YP2Debug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("shoutcastsourcedebug",SHOUTcastSourceDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("uvox2sourcedebug",UVOX2SourceDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("shoutcast1clientdebug",SHOUTcast1ClientDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("shoutcast2clientdebug",SHOUTcast2ClientDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("relayshoutcastdebug",RelaySHOUTcastDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("relayuvoxdebug",RelayUVOXDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("relaydebug",RelayDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("streamdatadebug",StreamDataDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("httpstyledebug",HTTPStyleDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("statsdebug",StatsDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("microserverdebug",MicroServerDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("threadrunnerdebug",ThreadRunnerDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("rtmpclientdebug",RTMPClientDebug && DNASDebug || DNASDebugMode==1,0);
		scscl+=OutInt("webclientdebug",WebClientDebug && DNASDebug || DNASDebugMode==1,0);
	}

	if(BuilderViewMode==1) {
		if(DJCipher!="" && DJCipher!="foobar")scscl+="uvoxcipherkey="+DJCipher+NL;

		if(DNASIntroFile!="") {
			scscl+="introfile="+MarryBasePathAndFile(DNASBasePath,DNASIntroFile)+NL;
		}
		if(DNASBackupFile!="") {
			scscl+="backupfile="+MarryBasePathAndFile(DNASBasePath,DNASBackupFile)+NL;
		}

		if(DNASAdminPageTheme!="v1" && DNASAdminPageTheme!="v2" && DNASAdminPageTheme!="") {
			scscl+="admincssfile="+MarryBasePathAndFile(DNASBasePath,DNASAdminPageTheme)+NL;
		} else {
			if(DNASAdminPageTheme!="" && DNASAdminPageTheme!="v2") {
				scscl+="admincssfile="+DNASAdminPageTheme+NL;
			}
		}

		scscl+=OutStr("robotstxtfile",RobotsTxt,"");
		scscl+=OutStr("faviconfile",DNASAdminPageFavIcon,"");
		scscl+=OutStr("faviconmimetype",DNASAdminPageFavIconMimeType,"image/x-icon");

		scscl+=OutStr("maxspecialfilesize",MaxSpecialFileSize,30000000);
		scscl+=OutStr("songhistory",SongHistory,10);
		if(BuilderViewMode==1) scscl+=OutInt("namelookups",NameLookups,0);
		scscl+=OutStr("maxheaderlinesize",MaxHeaderLineSize,2048);
		scscl+=OutStr("maxheaderlinecount",MaxHeaderLineCount,100);
		scscl+=OutStr("buffertype",BufferType,0);
		switch(BufferType) {
			case 0:
				scscl+=OutStr("fixedbuffersize",FixedBufferSize,1048576);
			break;
			case 1:
				scscl+=OutStr("adaptivebuffersize",AdaptiveBufferSize,1);
			break;
		}
		scscl+=OutStr("bufferhardlimit",BufferHardLimit,16777216);
	}

	scscl+="password="+Password+NL;
	scscl+="adminpassword="+AdminPassword+NL;
	if(BuilderViewMode==1) {
		if(DNASFlashPolicyFile!="") {
			scscl+=OutStr("flashpolicyfile",MarryBasePathAndFile(DNASBasePath,DNASFlashPolicyFile),"crossdomain.xml");
		}
		if(DNASFlashPolicyServerPort>0 && DNASFlashPolicyServerPort<65536 && DNASFlashPolicyServerPort!=NaN && DNASFlashPolicyServerPort!=null) {
			scscl+="flashpolicyserverport="+DNASFlashPolicyServerPort+NL;
		} else {
			if(!GenerateMinimal) {
				scscl+="flashpolicyserverport=0"+NL;
			}
		}
	}
	scscl+=OutStr("portbase",PortBase,8000);
	if(SourceBindAddress!="") {
		scscl+="srcip="+SourceBindAddress+NL;
	}
	if(DestinationBindAddress!="") {
		scscl+="dstip="+DestinationBindAddress+NL;
	}
	/*if(Mode==1) {
		if(!GenerateMinimal) {
			scscl+="requirestreamconfigs=0"+NL;
		}
		scscl+="yp2=0"+NL;
	} else */{
		scscl+="requirestreamconfigs=1"+NL;
		if(!GenerateMinimal) {
			scscl+="yp2=1"+NL;
		}
	}
	scscl+=OutInt("log",EnableLogging,1);
	scscl+=OutInt("screenlog",Screenlog,1);
	scscl+=OutInt("logclients",ClientConnectLog,1);
	scscl+="logfile="+MarryBasePathAndFile(DNASBasePath,DNASLogFile)+NL;
	scscl+=OutInt("w3cenable",W3CLogging,1);
	scscl+="w3clog="+MarryBasePathAndFile(DNASBasePath,W3CLogFile)+NL;
	if(BuilderViewMode==1 && TitleFormat!="") {
		scscl+="titleformat="+TitleFormat+NL;
	}
	if(BuilderViewMode==1 && URLFormat!="") {
		scscl+="urlformat="+URLFormat+NL;
	}
	scscl+=OutStr("publicserver",DNASPublic,"default");
	if(BuilderViewMode==1) {
		scscl+=OutStr("ypaddr",YPAddr,"yp.shoutcast.com");
		scscl+=OutStr("ypport",YPPort,80);
		scscl+=OutStr("ypPath",YPPath,"/yp2");
		scscl+=OutStr("ypTimeout",YPTimeout,60);
		/*if(Mode==1) {
			scscl+=OutStr("ypmaxretries",YPMaxRetries,10);
			scscl+=OutStr("ypreportinterval",YPReportInterval,300);
			scscl+=OutStr("ypminreportinterval",YPMinReportInterval,10);
			scscl+=OutStr("metainterval",MetaInterval,8192);
		}*/
	}
	scscl+="banfile="+MarryBasePathAndFile(DNASBasePath,BanFile)+NL;
	scscl+="ripfile="+MarryBasePathAndFile(DNASBasePath,RipFile)+NL;
	scscl+=OutInt("riponly",RipOnly,0);
	if(!GenerateMinimal) {
		scscl+="savebanlistonexit=1"+NL;
		scscl+="saveriplistonexit=1"+NL;
	}
	scscl+=OutStr("maxuser",MaxListeners,32);
	scscl+=OutStr("listenertime",ListenerTime,0);
	if(BuilderViewMode==1) {
		scscl+=OutInt("autodumpusers",AutoDumpUsers,0);
		scscl+=OutInt("hidestats",HideStats,0);
	}

	/*if(Mode==2) */{
		var rindex;
		for(var i=0;i<NumOfEndPoints;i++) {
			scscl+=NL;
			sctcl+=NL;

			rindex=i+1;
			scscl+="streamid"+OutServIndex(rindex)+rindex+NL;
			var epsp = EndPointPathInputArray[i].value;
			if(epsp!="" && (epsp!="/stream/"+rindex+"/") && (epsp!="stream/"+rindex+"/")) {
				scscl+="streampath"+OutServIndex(rindex)+EndPointPathInputArray[i].value+NL;
			}
			var epmu=EndPointMaxUserInputArray[i].value;
			if(epmu!="") {
				scscl+="streammaxuser"+OutServIndex(rindex)+epmu+NL;
			}
			var epah=EndPointAuthHashInputArray[i].value;
			if(epah=="") {
				epah=DefaultAuthHash;
			}
			if(epah!="") {
				scscl+="streamauthhash"+OutServIndex(rindex)+epah+NL;
			}
			var epp=EndPointPasswordInputArray[i].value;
			if(epp!="") {
				scscl+="streampassword"+OutServIndex(rindex)+epp+NL;
			}
			var epap=EndPointAdminPasswordInputArray[i].value;
			if(epap!="") {
				scscl+="streamadminpassword"+OutServIndex(rindex)+epap+NL;
			}

			if(EndPointNameInputArray[i].value == ("endpoint"+OutTransIndex(i,1))) {
				sctcl+="endpointname"+OutTransIndex(i,1)+EndPointNameInputArray[i].value+NL;
			}
			if(!TranscoderInherit) {
				sctcl+="protocol"+OutTransIndex(i,1)+"2"+NL;
				sctcl+="serverip"+OutTransIndex(i,1)+DNASIP+NL;
				sctcl+="serverport"+OutTransIndex(i,1)+PortBase+NL;
				sctcl+="streamid"+OutTransIndex(i,1)+rindex+NL;
				//sctcl+="uvoxuserid"+OutTransIndex(i,1)+"admin"+NL;
				if(epp!="") {
					sctcl+="password"+OutTransIndex(i,1)+epp+NL;
				} else {
					sctcl+="password"+OutTransIndex(i,1)+Password+NL;
				}
			}
			var ec=EndPointTypeSelectArray[i].value;
			sctcl+=OutTransStr("encoder",OutTransIndex(i),ec,"aacp");
			if(ec=="mp3") {
				sctcl+=OutTransStr("mp3quality",OutTransIndex(i),EndPointMP3QualitySelectArray[i].value,0);
			}
			sctcl+=OutTransStr("bitrate",OutTransIndex(i),EndPointBitrateInputArray[i].value,96000);
			sctcl+=OutTransStr("samplerate",OutTransIndex(i),EndPointSamplerateInputArray[i].value,44100);
			sctcl+=OutTransStr("channels",OutTransIndex(i),EndPointNumchnsInputArray[i].value,2);
			if(rindex!=1) {
				sctcl+=OutTransStr("streamtitle",OutTransIndex(i),EndPointTitleInputArray[i].value,0);
			}
		}
	}/* else {
		sctcl+=NL;
		sctcl+="serverport="+PortBase+NL;
		sctcl+="serverip="+DNASIP+NL;
		if(!GenerateMinimal) {
			if(!TranscoderInherit) {
				sctcl+="protocol=1"+NL;
			}
		}
		var ec=EndPointTypeSelectArray[0].value;
		sctcl+=OutStr("encoder",ec,"aacp");
		if(ec=="mp3") {
			sctcl+=OutStr("mp3quality",EndPointMP3QualitySelectArray[0].value,0);
		}
		sctcl+=OutStr("bitrate",EndPointBitrateInputArray[0].value,96000);
		sctcl+=OutStr("samplerate",EndPointSamplerateInputArray[0].value,44100);
		sctcl+=OutStr("channels",EndPointNumchnsInputArray[0].value,2);
	}*/

	scccl+="</eventlist>"+NL;

	try{SCServLinesTextArea.value=scscl;}catch(e){}
	try{SCTransLinesTextArea.value=sctcl;}catch(e){}
	try{SCCalendarLinesTextArea.value=scccl;}catch(e){}
	PopUpdateKill();
}

var LastHelpObj=0;
function DoHelpUpdate(Obj) {
	if(LastHelpObj==Obj) return;
	if(HelperTextDBKeysArray.length<1)return;
	if(HelperTextDBValuesArray.length<1)return;
	LastHelpObj=Obj;
	if(Obj==null) {
		// TODO
		if(HelperTextDBKeysArray.length > 1) {// uht("Move your mouse over an option to get additional information about that option.");
		uht("Select an option or move your mouse over it to get additional information about that option.<br/><br/>"+
			"Changes made in the options pane (to the left) appear in the 'Generated Configurations' at the bottom of "+
			"this page where you can then download the configuration files or copy + paste the configuration settings into a new or existing configuration files.");
		}
		return;
	}
	if(Obj.id=="") {
		return;
	}
	for(var i=0;i<HelperTextDBKeysArray.length;i++) {
		var uu=HelperTextDBKeysArray[i].split("\t");
		var id=Obj.id;
		if(id==uu[0]) {
			uht(HelperTextDBValuesArray[i]);
			return;
		}
		if(uu.length>1) {
			if(id.indexOf(uu[0])==0) {
				if(id.indexOf(uu[1])==(id.length-uu[1].length)) {
					uht(HelperTextDBValuesArray[i]);
					return;
				}
			}
		}
	}
	if(HelperTextDBKeysArray.length > 1) uht("No information is available for the option selected at this time.");
}

function onObjFocused(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	DoHelpUpdate(obj);
}

function onObjMousedOver(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	DoHelpUpdate(obj);
}

function AETFC(eo,ee) {
	if(eo==null)return;
	if(eo.type=="checkbox" || eo.type=="button" || eo.type=="radio") {
		eo.onclick=ee;
	} else {
		eo.onchange=ee;
	}
	eo.onkeyup=ee;
	eo.onfocus=onObjFocused;
	eo.onmouseover=onObjMousedOver;
}

function DoDeleteDJ(obj) {
	var dji=DJDeleteButtonArray.indexOf(obj);
	NumOfDJs--;
	var t=NumOfDJsHidden.onchange;
	NumOfDJsHidden.onchange=null;
	NumOfDJsHidden.value=NumOfDJs;
	SetObjCookie(NumOfDJsHidden);
	NumOfDJsHidden.onchange=t;
	for(var i=dji;i<NumOfDJs;i++) {
		DJLoginInputArray[i].value=DJLoginInputArray[i+1].value;
		DJLoginArray[i]=DJLoginArray[i+1];
		SetObjCookie(DJLoginInputArray[i]);

		DJPasswordInputArray[i].value=DJPasswordInputArray[i+1].value;
		DJPasswordArray[i]=DJPasswordArray[i+1];
		SetObjCookie(DJPasswordInputArray[i]);

		DJPriorityInputArray[i].value=DJPriorityInputArray[i+1].value;
		DJPriorityArray[i].value=DJPriorityArray[i+1].value;
		SetObjCookie(DJPriorityInputArray[i]);
	}
	var vi=DJLoginInputArray.pop();
	EraseObjCookie(vi);
	var vi=DJPasswordInputArray.pop();
	EraseObjCookie(vi);
	var vi=DJPriorityInputArray.pop();
	EraseObjCookie(vi);
	var vi=DJDeleteButtonArray.pop();
	EraseObjCookie(vi);
	createCookie("NumOfDJsHidden",NumOfDJs);
	MultiDJSpanUpdate();
}

function DoAddDJ() {
	NumOfDJs++;
	var t=NumOfDJsHidden.onchange;
	NumOfDJsHidden.onchange=null;
	NumOfDJsHidden.value=NumOfCalendarEvents;
	SetObjCookie(NumOfDJsHidden);
	NumOfDJsHidden.onchange=t;
	MultiDJSpanUpdate();
}

function MultiDJSpanUpdate() {
	delete DJLoginInputArray;
	delete DJPasswordInputArray;
	delete DJPriorityInputArray;
	delete DJCalendarAddButtonArray;
	delete DJDeleteButtonArray;
	delete DJLoginArray;
	delete DJPasswordArray;
	delete DJPriorityArray;
	var str="";
	MultiDJSpan.innerHTML=str;
	DJLoginInputArray=new Array(NumOfDJs);
	DJPasswordInputArray=new Array(NumOfDJs);
	DJPriorityInputArray=new Array(NumOfDJs);
	DJCalendarAddButtonArray=new Array(NumOfDJs);
	DJDeleteButtonArray=new Array(NumOfDJs);
	DJLoginArray=new Array(NumOfDJs);
	DJPasswordArray=new Array(NumOfDJs);
	DJPriorityArray=new Array(NumOfDJs);
	var c=NumOfDJs;
	for(var rindex=0;rindex<NumOfDJs;rindex++) {
		str+="<table border=\"0\">";

		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">";
		var naid="DJ"+rindex+"DeleteButton";
		str+="<br/><input name=\""+naid+"\" id=\""+naid+"\" type=\"button\" class=\"button\" value=\"Remove DJ\"/></td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="DJ"+rindex+"LoginInput";
		str+="DJ login name</td><td><input name=\""+naid+"\" id=\""+naid+"\"/>";
		str+="</td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="DJ"+rindex+"PasswordInput";
		str+="DJ password</td><td><input name=\""+naid+"\" id=\""+naid+"\"/>";
		str+="</td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="DJ"+rindex+"PriorityInput";
		str+="DJ priority</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"1\"/>";
		str+="</td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="DJ"+rindex+"CalendarAddButton";
		str+="<input name=\""+naid+"\" id=\""+naid+"\" type=\"button\" class=\"button\" value=\"Calendar\"/></td><td>Add DJ to the calendar";
		str+="</td></tr>";

		str+="</table>";
	}
	MultiDJSpan.innerHTML=str;
	for(var rindex=0;rindex<NumOfDJs;rindex++) {
		var naid="DJ"+rindex+"LoginInput";
		var vi=$(naid);
		DJLoginInputArray[rindex]=vi;
		DJLoginArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onDJLoginInputChanged);

		var naid="DJ"+rindex+"PasswordInput";
		vi=$(naid);
		DJPasswordInputArray[rindex]=vi;
		DJPasswordArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onDJPasswordInputChanged);

		var naid="DJ"+rindex+"PriorityInput";
		vi=$(naid);
		DJPriorityInputArray[rindex]=vi;
		DJPriorityArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onDJPriorityInputChanged);

		var naid="DJ"+rindex+"CalendarAddButton";
		vi=$(naid);
		DJCalendarAddButtonArray[rindex]=vi;
		AETFC(vi,onDJCalendarAddButtonClicked);

		var naid="DJ"+rindex+"DeleteButton";
		vi=$(naid);
		DJDeleteButtonArray[rindex]=vi;
		AETFC(vi,onDJDeleteButtonClicked);
	}
}

function DoCalendarEventTypeSelectUpdate(obj) {
	var i=CalendarEventTypeSelectArray.indexOf(obj);
	var y=$("Calendar"+i+"DJEventSpan");
	if(obj.value!="dj") {
		DoObjShowHide(0, y);
	} else {
		DoObjShowHide(1, y);
	}
	var y=$("Calendar"+i+"PlaylistEventSpan");
	if(obj.value!="playlist") {
		DoObjShowHide(0, y);
	} else {
		DoObjShowHide(1, y);
	}
	var y=$("Calendar"+i+"RelayEventSpan");
	if(obj.value!="relay") {
		DoObjShowHide(0, y);
	} else {
		DoObjShowHide(1, y);
	}
}

function onNumOfCalendarEventsHiddenChanged() {
	NumOfCalendarEvents=(NumOfCalendarEventsHidden.value)*1;
	SetObjCookie(NumOfCalendarEventsHidden);
	MultiCalendarSpanUpdate();
	DoUpdate();
}

function onCalendarEventTypeSelectChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarEventTypeSelectArray.indexOf(obj)
	if(i<0)return;
	CalendarEventTypeArray[i]=(CalendarEventTypeSelectArray[i].value);
	SetObjCookie(CalendarEventTypeSelectArray[i]);
	DoCalendarEventTypeSelectUpdate(obj);
	DoUpdate();
}

function onCalendarDJNameInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarDJNameInputArray.indexOf(obj)
	if(i<0)return;
	CalendarDJNameArray[i]=(CalendarDJNameInputArray[i].value);
	SetObjCookie(CalendarDJNameInputArray[i]);
	DoUpdate();
}

function onCalendarDJArchiveSelectChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarDJArchiveSelectArray.indexOf(obj)
	if(i<0)return;
	CalendarDJArchiveArray[i]=(CalendarDJArchiveSelectArray[i].value);
	SetObjCookie(CalendarDJArchiveSelectArray[i]);
	DoUpdate();
}

function onCalendarPlaylistNameInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarPlaylistNameInputArray.indexOf(obj)
	if(i<0)return;
	CalendarPlaylistNameArray[i]=(CalendarPlaylistNameInputArray[i].value);
	SetObjCookie(CalendarPlaylistNameInputArray[i]);
	DoUpdate();
}

function onCalendarPlaylistLoopAtEndCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarPlaylistLoopAtEndCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarPlaylistLoopAtEndArray[i]=(CalendarPlaylistLoopAtEndCheckBoxArray[i].checked);
	SetObjCookie(CalendarPlaylistLoopAtEndCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarPlaylistShuffleSelectChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarPlaylistShuffleSelectArray.indexOf(obj)
	if(i<0)return;
	CalendarPlaylistShuffleArray[i]=(CalendarPlaylistShuffleSelectArray[i].value);
	SetObjCookie(CalendarPlaylistShuffleSelectArray[i]);
	DoUpdate();
}

function onCalendarPlaylistPriorityInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarPlaylistPriorityInputArray.indexOf(obj)
	if(i<0)return;
	CalendarPlaylistPriorityArray[i]=(CalendarPlaylistPriorityInputArray[i].value);
	SetObjCookie(CalendarPlaylistPriorityInputArray[i]);
	DoUpdate();
}

function onCalendarRelayURLInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarRelayURLInputArray.indexOf(obj)
	if(i<0)return;
	CalendarRelayURLArray[i]=(CalendarRelayURLInputArray[i].value);
	SetObjCookie(CalendarRelayURLInputArray[i]);
	DoUpdate();
}

function onCalendarRelayPriorityInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarRelayPriorityInputArray.indexOf(obj)
	if(i<0)return;
	CalendarRelayPriorityArray[i]=(CalendarRelayPriorityInputArray[i].value);
	SetObjCookie(CalendarRelayPriorityInputArray[i]);
	DoUpdate();
}

function onCalendarPerCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarPerCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarPerArray[i]=(CalendarPerCheckBoxArray[i].checked);
	SetObjCookie(CalendarPerCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarSunCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarSunCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarSunArray[i]=(CalendarSunCheckBoxArray[i].checked);
	SetObjCookie(CalendarSunCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarMonCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarMonCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarMonArray[i]=(CalendarMonCheckBoxArray[i].checked);
	SetObjCookie(CalendarMonCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarTueCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarTueCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarTueArray[i]=(CalendarTueCheckBoxArray[i].checked);
	SetObjCookie(CalendarTueCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarWedCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarWedCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarWedArray[i]=(CalendarWedCheckBoxArray[i].checked);
	SetObjCookie(CalendarWedCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarThuCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarThuCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarThuArray[i]=(CalendarThuCheckBoxArray[i].checked);
	SetObjCookie(CalendarThuCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarFriCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarFriCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarFriArray[i]=(CalendarFriCheckBoxArray[i].checked);
	SetObjCookie(CalendarFriCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarSatCheckBoxClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarSatCheckBoxArray.indexOf(obj)
	if(i<0)return;
	CalendarSatArray[i]=(CalendarSatCheckBoxArray[i].checked);
	SetObjCookie(CalendarSatCheckBoxArray[i]);
	DoUpdate();
}

function onCalendarStartDateInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarStartDateInputArray.indexOf(obj)
	if(i<0)return;
	CalendarStartDateArray[i]=(CalendarStartDateInputArray[i].value);
	SetObjCookie(CalendarStartDateInputArray[i]);
	DoUpdate();
}

function onCalendarEndDateInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarEndDateInputArray.indexOf(obj)
	if(i<0)return;
	CalendarEndDateArray[i]=(CalendarEndDateInputArray[i].value);
	SetObjCookie(CalendarEndDateInputArray[i]);
	DoUpdate();
}

function onCalendarStartTimeInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarStartTimeInputArray.indexOf(obj)
	if(i<0)return;
	CalendarStartTimeArray[i]=(CalendarStartTimeInputArray[i].value);
	SetObjCookie(CalendarStartTimeInputArray[i]);
	DoUpdate();
}

function onCalendarDurationInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarDurationInputArray.indexOf(obj)
	if(i<0)return;
	CalendarDurationArray[i]=(CalendarDurationInputArray[i].value);
	SetObjCookie(CalendarDurationInputArray[i]);
	DoUpdate();
}

function onCalendarTimeOffsetInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=CalendarTimeOffsetInputArray.indexOf(obj)
	if(i<0)return;
	CalendarTimeOffsetArray[i]=(CalendarTimeOffsetInputArray[i].value);
	SetObjCookie(CalendarTimeOffsetInputArray[i]);
	DoUpdate();
}

function DoAddCalendarEvent() {
	NumOfCalendarEvents++;
	var t=NumOfCalendarEventsHidden.onchange;
	NumOfCalendarEventsHidden.onchange=null;
	NumOfCalendarEventsHidden.value=NumOfCalendarEvents;
	SetObjCookie(NumOfCalendarEventsHidden);
	NumOfCalendarEventsHidden.onchange=t;
	MultiCalendarSpanUpdate();
}

function onCalendarEventAddButtonClicked() {
	// check so that we only add in more events if the last attempt was ok
	if(!NumOfCalendarEvents ||
		CalendarEventTypeSelectArray[NumOfCalendarEvents-1].value=="dj" && CalendarDJNameInputArray[NumOfCalendarEvents-1].value ||
		CalendarEventTypeSelectArray[NumOfCalendarEvents-1].value=="playlist" && CalendarPlaylistNameInputArray[NumOfCalendarEvents-1].value ||
		CalendarEventTypeSelectArray[NumOfCalendarEvents-1].value=="relay" && CalendarRelayURLInputArray[NumOfCalendarEvents-1].value) {
		DoAddCalendarEvent();
		DoUpdate();
	}
}

function DoDeleteEvent(obj) {
	var dji=CalendarEventDeleteButtonArray.indexOf(obj);
	NumOfCalendarEvents--;
	var t=NumOfCalendarEventsHidden.onchange;
	NumOfCalendarEventsHidden.onchange=null;
	NumOfCalendarEventsHidden.value=NumOfCalendarEvents;
	SetObjCookie(NumOfCalendarEventsHidden);
	NumOfCalendarEventsHidden.onchange=t;
	for(var i=dji;i<NumOfCalendarEvents;i++) {

		CalendarEventTypeSelectArray[i].selectedIndex=CalendarEventTypeSelectArray[i+1].selectedIndex;
		CalendarEventTypeArray[i]=CalendarEventTypeArray[i+1];
		SetObjCookie(CalendarEventTypeSelectArray[i]);
		DoCalendarEventTypeSelectUpdate(CalendarEventTypeSelectArray[i]);

		CalendarDJNameInputArray[i].value=CalendarDJNameInputArray[i+1].value;
		CalendarDJNameArray[i]=CalendarDJNameArray[i+1];
		SetObjCookie(CalendarDJNameInputArray[i]);

		CalendarDJArchiveSelectArray[i].selectedIndex=CalendarDJArchiveSelectArray[i+1].selectedIndex;
		CalendarDJArchiveArray[i]=CalendarDJArchiveArray[i+1];
		SetObjCookie(CalendarDJArchiveSelectArray[i]);

		CalendarPlaylistNameInputArray[i].value=CalendarPlaylistNameInputArray[i+1].value;
		CalendarPlaylistNameArray[i]=CalendarPlaylistNameArray[i+1];
		SetObjCookie(CalendarPlaylistNameInputArray[i]);

		CalendarPlaylistLoopAtEndCheckBoxArray[i].checked=CalendarPlaylistLoopAtEndCheckBoxArray[i+1].checked;
		CalendarPlaylistLoopAtEndArray[i]=CalendarPlaylistLoopAtEndArray[i+1];
		SetObjCookie(CalendarPlaylistLoopAtEndCheckBoxArray[i]);

		CalendarPlaylistShuffleSelectArray[i].selectedIndex=CalendarPlaylistShuffleSelectArray[i+1].selectedIndex;
		CalendarPlaylistShuffleArray[i]=CalendarPlaylistShuffleArray[i+1];
		SetObjCookie(CalendarPlaylistShuffleSelectArray[i]);

		CalendarPlaylistPriorityInputArray[i].value=CalendarPlaylistPriorityInputArray[i+1].value;
		CalendarPlaylistPriorityArray[i]=CalendarPlaylistPriorityArray[i+1];
		SetObjCookie(CalendarPlaylistPriorityInputArray[i]);

		CalendarRelayURLInputArray[i].value=CalendarRelayURLInputArray[i+1].value;
		CalendarRelayURLArray[i]=CalendarRelayURLArray[i+1];
		SetObjCookie(CalendarRelayURLInputArray[i]);

		CalendarRelayPriorityInputArray[i].value=CalendarRelayPriorityInputArray[i+1].value;
		CalendarRelayPriorityArray[i]=CalendarRelayPriorityArray[i+1];
		SetObjCookie(CalendarRelayPriorityInputArray[i]);

		CalendarStartDateInputArray[i].value=CalendarStartDateInputArray[i+1].value;
		CalendarStartDateArray[i]=CalendarStartDateArray[i+1];
		SetObjCookie(CalendarStartDateInputArray[i]);

		CalendarEndDateInputArray[i].value=CalendarEndDateInputArray[i+1].value;
		CalendarEndDateArray[i]=CalendarEndDateArray[i+1];
		SetObjCookie(CalendarEndDateInputArray[i]);

		CalendarStartTimeInputArray[i].value=CalendarStartTimeInputArray[i+1].value;
		CalendarStartTimeArray[i].value=CalendarStartTimeArray[i+1];
		SetObjCookie(CalendarStartTimeInputArray[i]);

		CalendarDurationInputArray[i].value=CalendarDurationInputArray[i+1].value;
		CalendarDurationArray[i]=CalendarDurationArray[i+1];
		SetObjCookie(CalendarDurationInputArray[i]);

		CalendarTimeOffsetInputArray[i].value=CalendarTimeOffsetInputArray[i+1].value;
		CalendarTimeOffsetArray[i].value=CalendarTimeOffsetArray[i+1];
		SetObjCookie(CalendarTimeOffsetInputArray[i]);

		CalendarPerCheckBoxArray[i].checked=CalendarPerCheckBoxArray[i+1].checked;
		CalendarPerArray[i]=CalendarPerArray[i+1];
		SetObjCookie(CalendarPerCheckBoxArray[i]);

		CalendarSunCheckBoxArray[i].checked=CalendarSunCheckBoxArray[i+1].checked;
		CalendarSunArray[i]=CalendarSunArray[i+1];
		SetObjCookie(CalendarSunCheckBoxArray[i]);

		CalendarMonCheckBoxArray[i].checked=CalendarMonCheckBoxArray[i+1].checked;
		CalendarMonArray[i]=CalendarMonArray[i+1];
		SetObjCookie(CalendarMonCheckBoxArray[i]);

		CalendarTueCheckBoxArray[i].checked=CalendarTueCheckBoxArray[i+1].checked;
		CalendarTueArray[i]=CalendarTueArray[i+1];
		SetObjCookie(CalendarTueCheckBoxArray[i]);

		CalendarWedCheckBoxArray[i].checked=CalendarWedCheckBoxArray[i+1].checked;
		CalendarWedArray[i]=CalendarWedArray[i+1];
		SetObjCookie(CalendarWedCheckBoxArray[i]);

		CalendarThuCheckBoxArray[i].checked=CalendarThuCheckBoxArray[i+1].checked;
		CalendarThuArray[i]=CalendarThuArray[i+1];
		SetObjCookie(CalendarThuCheckBoxArray[i]);

		CalendarFriCheckBoxArray[i].checked=CalendarFriCheckBoxArray[i+1].checked;
		CalendarFriArray[i]=CalendarFriArray[i+1];
		SetObjCookie(CalendarFriCheckBoxArray[i]);

		CalendarSatCheckBoxArray[i].checked=CalendarSatCheckBoxArray[i+1].checked;
		CalendarSatArray[i]=CalendarSatArray[i+1];
		SetObjCookie(CalendarSatCheckBoxArray[i]);
	}
	var vi=CalendarEventTypeSelectArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarDJNameInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarDJArchiveSelectArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarPlaylistNameInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarPlaylistLoopAtEndCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarPlaylistShuffleSelectArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarPlaylistPriorityInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarRelayURLInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarRelayPriorityInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarStartDateInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarEndDateInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarStartTimeInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarDurationInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarTimeOffsetInputArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarPerCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarSunCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarMonCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarTueCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarWedCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarThuCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarFriCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarSatCheckBoxArray.pop();
	EraseObjCookie(vi);

	var vi=CalendarEventDeleteButtonArray.pop();

	MultiCalendarSpanUpdate();
}

function onCalendarEventDeleteButtonClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	DoDeleteEvent(obj);
	DoUpdate();
}

function MultiCalendarSpanUpdate() {
	delete CalendarEventDeleteButtonArray;
	delete CalendarEventTypeSelectArray;
	delete CalendarDJNameInputArray;
	delete CalendarDJArchiveSelectArray;
	delete CalendarPlaylistNameInputArray;
	delete CalendarPlaylistLoopAtEndCheckBoxArray;
	delete CalendarPlaylistShuffleSelectArray;
	delete CalendarPlaylistPriorityInputArray;
	delete CalendarRelayURLInputArray;
	delete CalendarRelayPriorityInputArray;
	delete CalendarPerCheckBoxArray;
	delete CalendarSunCheckBoxArray;
	delete CalendarMonCheckBoxArray;
	delete CalendarTueCheckBoxArray;
	delete CalendarWedCheckBoxArray;
	delete CalendarThuCheckBoxArray;
	delete CalendarFriCheckBoxArray;
	delete CalendarSatCheckBoxArray;
	delete CalendarStartDateInputArray;
	delete CalendarEndDateInputArray;
	delete CalendarStartTimeInputArray;
	delete CalendarDurationInputArray;
	delete CalendarTimeOffsetInputArray;
	delete CalendarEventTypeArray;
	delete CalendarDJNameArray;
	delete CalendarDJArchiveArray;
	delete CalendarPlaylistNameArray;
	delete CalendarPlaylistLoopAtEndArray;
	delete CalendarPlaylistShuffleArray;
	delete CalendarPlaylistPriorityArray;
	delete CalendarRelayURLArray;
	delete CalendarRelayPriorityArray;
	delete CalendarPerArray;
	delete CalendarSunArray;
	delete CalendarMonArray;
	delete CalendarTueArray;
	delete CalendarWedArray;
	delete CalendarThuArray;
	delete CalendarFriArray;
	delete CalendarSatArray;
	delete CalendarStartDateArray;
	delete CalendarEndDateArray;
	delete CalendarStartTimeArray;
	delete CalendarDurationArray;
	delete CalendarTimeOffsetArray;
	var str="";
	MultiCalendarSpan.innerHTML=str;
	CalendarEventDeleteButtonArray = new Array(NumOfCalendarEvents);
	CalendarEventTypeSelectArray = new Array(NumOfCalendarEvents);
	CalendarDJNameInputArray = new Array(NumOfCalendarEvents);
	CalendarDJArchiveSelectArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistNameInputArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistLoopAtEndCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistShuffleSelectArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistPriorityInputArray = new Array(NumOfCalendarEvents);
	CalendarRelayURLInputArray = new Array(NumOfCalendarEvents);
	CalendarRelayPriorityInputArray = new Array(NumOfCalendarEvents);
	CalendarPerCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarSunCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarMonCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarTueCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarWedCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarThuCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarFriCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarSatCheckBoxArray = new Array(NumOfCalendarEvents);
	CalendarStartDateInputArray = new Array(NumOfCalendarEvents);
	CalendarEndDateInputArray = new Array(NumOfCalendarEvents);
	CalendarStartTimeInputArray = new Array(NumOfCalendarEvents);
	CalendarDurationInputArray = new Array(NumOfCalendarEvents);
	CalendarTimeOffsetInputArray = new Array(NumOfCalendarEvents);
	CalendarEventTypeArray = new Array(NumOfCalendarEvents);
	CalendarDJNameArray = new Array(NumOfCalendarEvents);
	CalendarDJArchiveArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistNameArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistLoopAtEndArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistShuffleArray = new Array(NumOfCalendarEvents);
	CalendarPlaylistPriorityArray = new Array(NumOfCalendarEvents);
	CalendarRelayURLArray = new Array(NumOfCalendarEvents);
	CalendarRelayPriorityArray = new Array(NumOfCalendarEvents);
	CalendarPerArray = new Array(NumOfCalendarEvents);
	CalendarSunArray = new Array(NumOfCalendarEvents);
	CalendarMonArray = new Array(NumOfCalendarEvents);
	CalendarTueArray = new Array(NumOfCalendarEvents);
	CalendarWedArray = new Array(NumOfCalendarEvents);
	CalendarThuArray = new Array(NumOfCalendarEvents);
	CalendarFriArray = new Array(NumOfCalendarEvents);
	CalendarSatArray = new Array(NumOfCalendarEvents);
	CalendarStartDateArray = new Array(NumOfCalendarEvents);
	CalendarEndDateArray = new Array(NumOfCalendarEvents);
	CalendarStartTimeArray = new Array(NumOfCalendarEvents);
	CalendarDurationArray = new Array(NumOfCalendarEvents);
	CalendarTimeOffsetArray = new Array(NumOfCalendarEvents);
	for(var rindex=0;rindex<NumOfCalendarEvents;rindex++) {
		str+="<br/><br/>";

		str+="<table style=\"width:100%\">";

		str+="<tr><td>";
		str+="<table style=\"width:100%\">"
		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">";
		var naid="Calendar"+(rindex)+"EventDeleteButton";
		str+="<input name=\""+naid+"\" id=\""+naid+"\" type=\"button\" class=\"button\" value=\"Remove Event\"/>";
		str+="</td></tr>";
		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">";
		var naid="Calendar"+(rindex)+"EventTypeSelect";
		str+="Type</td><td><select name=\""+naid+"\" id=\""+naid+"\"><option value=\"dj\">DJ</option><option value=\"playlist\">Playlist</option><option value=\"relay\">Relay</option></select>";
		str+="</td></tr></table></td></tr>";

		str+="<tr><td>";

		var naid="Calendar"+(rindex)+"DJEventSpan";
		str+="<span id=\""+naid+"\">";
		str+="<table style=\"width:100%\">";
		var naid="Calendar"+(rindex)+"DJNameInput";
		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">DJ name</td><td>";
		// TODO
		/*if(NumOfDJs > 0) {
			str+="<select name=\""+naid+"\" id=\""+naid+"\"/>";
			var sa=0;
			for(var i=0;i<NumOfDJs;i++) {
				var dl=DJLoginArray[i];
				if(dl==""){sa++;continue;}
				str+="<option value=\""+dl+"\">"+dl+"</option>";
			}
		} else*/ {
			str+="<input name=\""+naid+"\" id=\""+naid+"\"/>";
		}

		str+="</td></tr>";

		var naid="Calendar"+(rindex)+"DJArchiveSelect";
		str+="<tr><td class=\"ConfigTableDescTD\">Enable archive</td><td><select name=\""+naid+"\" id=\""+naid+"\"><option value=\"inherit\">Inherit</option><option value=\"1\">Yes</option><option value=\"0\">No</option></select></td></tr>";
		str+="</table>";
		str+="</span>";

		var naid="Calendar"+(rindex)+"PlaylistEventSpan";
		str+="<span id=\""+naid+"\">";
		str+="<table style=\"width:100%\">";
		var naid="Calendar"+(rindex)+"PlaylistNameInput";
		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">Playlist name</td><td>";
		// TODO
		/*if(NumOfPlaylists > 0) {
			str+="<select name=\""+naid+"\" id=\""+naid+"\"/>";
			var sa=0;
			for(var i=0;i<NumOfPlaylists;i++) {
				var pn=PlaylistMultiSymNameArray[i];
				if(pn==""){sa++;continue;}
				str+="<option value=\""+pn+"\">"+pn+"</option>";
			}
		} else*/ {
			str+="<input name=\""+naid+"\" id=\""+naid+"\"/>";
		}
		str+="</td></tr>";

		var naid="Calendar"+(rindex)+"PlaylistLoopAtEndCheckBox";
		str+="<tr><td class=\"ConfigTableDescTD\">Loop at playlist end</td><td><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\" checked=\"checked\"/></td></tr>";
		var naid="Calendar"+(rindex)+"PlaylistShuffleSelect";
		str+="<tr><td class=\"ConfigTableDescTD\">Shuffle</td><td><select name=\""+naid+"\" id=\""+naid+"\"><option value=\"inherit\">Inherit</option><option value=\"1\">Yes</option><option value=\"0\">No</option></select></td></tr>";
		var naid="Calendar"+(rindex)+"PlaylistPriorityInput";
		str+="<tr><td class=\"ConfigTableDescTD\">Priority</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"1\"/></td></tr>";
		str+="</table>";
		str+="</span>";

		var naid="Calendar"+(rindex)+"RelayEventSpan";
		str+="<span id=\""+naid+"\">";
		str+="<table style=\"width:100%\">";
		var naid="Calendar"+(rindex)+"RelayURLInput";
		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">Source URL</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"\"/></td></tr>";
		var naid="Calendar"+(rindex)+"RelayPriorityInput";
		str+="<tr><td class=\"ConfigTableDescTD\">Priority</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"1\"/></td></tr>";
		str+="</table>";
		str+="</span>";

		str+="</td></tr>";

		str+="<tr><td>";

		str+="<table style=\"width:100%\">";
		var naid="Calendar"+(rindex)+"StartDateInput";
		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">Start date</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"\"/></td></tr>";
		var naid="Calendar"+(rindex)+"EndDateInput";
		str+="<tr><td class=\"ConfigTableDescTD\">End date</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"\"/></td></tr>";
		var naid="Calendar"+(rindex)+"StartTimeInput";
		str+="<tr><td class=\"ConfigTableDescTD\">Start time</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"\"/></td></tr>";
		var naid="Calendar"+(rindex)+"DurationInput";
		str+="<tr><td class=\"ConfigTableDescTD\">Duration</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"\"/></td></tr>";
		var naid="Calendar"+(rindex)+"TimeOffsetInput";
		str+="<tr><td class=\"ConfigTableDescTD\">Time offset</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"\"/></td></tr>";
		str+="</table>";

		str+="<table align=\"center\"><tr><td>Sun</td><td>Mon</td><td>Tue</td><td>Wed</td><td>Thur</td><td>Fri</td><td>Sat</td><td>Periodic</td></tr><tr>";
		var naid="Calendar"+(rindex)+"SunCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		var naid="Calendar"+(rindex)+"MonCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		var naid="Calendar"+(rindex)+"TueCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		var naid="Calendar"+(rindex)+"WedCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		var naid="Calendar"+(rindex)+"ThuCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		var naid="Calendar"+(rindex)+"FriCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		var naid="Calendar"+(rindex)+"SatCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		var naid="Calendar"+(rindex)+"PerCheckBox";
		str+="<td style=\"text-align:center\"><input name=\""+naid+"\" id=\""+naid+"\" type=\"checkbox\"/></td>";
		str+="</tr></table>";

		str+="</td></tr>";
		str+="</table>";

		str+="</td></tr></table>";
	}

	MultiCalendarSpan.innerHTML=str;
	var vi;
	for(var rindex=0;rindex<NumOfCalendarEvents;rindex++) {
		var naid="Calendar"+(rindex)+"EventDeleteButton";
		vi=$(naid);
		CalendarEventDeleteButtonArray[rindex]=vi;
		AETFC(vi,onCalendarEventDeleteButtonClicked);

		var naid="Calendar"+(rindex)+"EventTypeSelect";
		vi=$(naid);
		CalendarEventTypeSelectArray[rindex]=vi;
		CalendarEventTypeArray[rindex]=GetObjCookie(vi);
		DoCalendarEventTypeSelectUpdate(vi);
		AETFC(vi,onCalendarEventTypeSelectChanged);

		var naid="Calendar"+(rindex)+"DJNameInput";
		vi=$(naid);
		CalendarDJNameInputArray[rindex]=vi;
		CalendarDJNameArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarDJNameInputChanged);

		var naid="Calendar"+(rindex)+"DJArchiveSelect";
		vi=$(naid);
		CalendarDJArchiveSelectArray[rindex]=vi;
		CalendarDJArchiveArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarDJArchiveSelectChanged);

		var naid="Calendar"+(rindex)+"PlaylistNameInput";
		vi=$(naid);
		CalendarPlaylistNameInputArray[rindex]=vi;
		CalendarPlaylistNameArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarPlaylistNameInputChanged);

		var naid="Calendar"+(rindex)+"PlaylistLoopAtEndCheckBox";
		vi=$(naid);
		CalendarPlaylistLoopAtEndCheckBoxArray[rindex]=vi;
		CalendarPlaylistLoopAtEndArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarPlaylistLoopAtEndCheckBoxClicked);

		var naid="Calendar"+(rindex)+"PlaylistShuffleSelect";
		vi=$(naid);
		CalendarPlaylistShuffleSelectArray[rindex]=vi;
		CalendarPlaylistShuffleArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarPlaylistShuffleSelectChanged);

		var naid="Calendar"+(rindex)+"PlaylistPriorityInput";
		vi=$(naid);
		CalendarPlaylistPriorityInputArray[rindex]=vi;
		CalendarPlaylistPriorityArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarPlaylistPriorityInputChanged);

		var naid="Calendar"+(rindex)+"RelayURLInput";
		vi=$(naid);
		CalendarRelayURLInputArray[rindex]=vi;
		CalendarRelayURLArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarRelayURLInputChanged);

		var naid="Calendar"+(rindex)+"RelayPriorityInput";
		vi=$(naid);
		CalendarRelayPriorityInputArray[rindex]=vi;
		CalendarRelayPriorityArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarRelayPriorityInputChanged);

		var naid="Calendar"+(rindex)+"PerCheckBox";
		vi=$(naid);
		CalendarPerCheckBoxArray[rindex]=vi;
		CalendarPerArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarPerCheckBoxClicked);

		var naid="Calendar"+(rindex)+"SunCheckBox";
		vi=$(naid);
		CalendarSunCheckBoxArray[rindex]=vi;
		CalendarSunArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarSunCheckBoxClicked);

		var naid="Calendar"+(rindex)+"MonCheckBox";
		vi=$(naid);
		CalendarMonCheckBoxArray[rindex]=vi;
		CalendarMonArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarMonCheckBoxClicked);

		var naid="Calendar"+(rindex)+"TueCheckBox";
		vi=$(naid);
		CalendarTueCheckBoxArray[rindex]=vi;
		CalendarTueArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarTueCheckBoxClicked);

		var naid="Calendar"+(rindex)+"WedCheckBox";
		vi=$(naid);
		CalendarWedCheckBoxArray[rindex]=vi;
		CalendarWedArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarWedCheckBoxClicked);

		var naid="Calendar"+(rindex)+"ThuCheckBox";
		vi=$(naid);
		CalendarThuCheckBoxArray[rindex]=vi;
		CalendarThuArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarThuCheckBoxClicked);

		var naid="Calendar"+(rindex)+"FriCheckBox";
		vi=$(naid);
		CalendarFriCheckBoxArray[rindex]=vi;
		CalendarFriArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarFriCheckBoxClicked);

		var naid="Calendar"+(rindex)+"SatCheckBox";
		vi=$(naid);
		CalendarSatCheckBoxArray[rindex]=vi;
		CalendarSatArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarSatCheckBoxClicked);

		var naid="Calendar"+(rindex)+"StartDateInput";
		vi=$(naid);
		CalendarStartDateInputArray[rindex]=vi;
		CalendarStartDateArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarStartDateInputChanged);

		var naid="Calendar"+(rindex)+"EndDateInput";
		vi=$(naid);
		CalendarEndDateInputArray[rindex]=vi;
		CalendarEndDateArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarEndDateInputChanged);

		var naid="Calendar"+(rindex)+"StartTimeInput";
		vi=$(naid);
		CalendarStartTimeInputArray[rindex]=vi;
		CalendarStartTimeArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarStartTimeInputChanged);

		var naid="Calendar"+(rindex)+"DurationInput";
		vi=$(naid);
		CalendarDurationInputArray[rindex]=vi;
		CalendarDurationArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarDurationInputChanged);

		var naid="Calendar"+(rindex)+"TimeOffsetInput";
		vi=$(naid);
		CalendarTimeOffsetInputArray[rindex]=vi;
		CalendarTimeOffsetArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onCalendarTimeOffsetInputChanged);
	}
}

function DoDeletePlaylistMulti(obj) {
	var dji=PlaylistMultiRemoveButtonArray.indexOf(obj);
	NumOfPlaylists--;
	var t=NumOfPlaylistsHidden.onchange;
	NumOfPlaylistsHidden.onchange=null;
	NumOfPlaylistsHidden.value=NumOfPlaylists;
	SetObjCookie(NumOfPlaylistsHidden);
	NumOfPlaylistsHidden.onchange=t;
	for(var i=dji;i<NumOfPlaylists;i++) {
		PlaylistMultiSymNameInputArray[i].value=PlaylistMultiSymNameInputArray[i+1].value;
		PlaylistMultiSymNameArray[i]=PlaylistMultiSymNameArray[i+1];
		SetObjCookie(PlaylistMultiSymNameInputArray[i]);

		PlaylistMultiFileInputArray[i].value=PlaylistMultiFileInputArray[i+1].value;
		PlaylistMultiFileArray[i]=PlaylistMultiFileArray[i+1];
		SetObjCookie(PlaylistMultiFileInputArray[i]);
	}
	var vi=PlaylistMultiSymNameInputArray.pop();
	EraseObjCookie(vi);
	var vi=PlaylistMultiFileInputArray.pop();
	EraseObjCookie(vi);
	var vi=PlaylistMultiRemoveButtonArray.pop();
	EraseObjCookie(vi);
	createCookie("NumOfPlaylistsHidden",NumOfDJs);
	MultiPlaylistSpanUpdate();
}

function onPlaylistMultiRemoveButtonClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	DoDeletePlaylistMulti(obj);
	DoUpdate();
}

function onPlaylistMultiCalendarAddButtonClicked(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var dji=PlaylistMultiCalendarAddButtonArray.indexOf(obj);
	var PMSymNameObj=PlaylistMultiSymNameInputArray[dji];
	if(PMSymNameObj==null || PMSymNameObj==undefined)return;
	if(PMSymNameObj.value=="")return;
	DoAddCalendarEvent();
	DoUpdate();
	var CalEvtTypeObj=CalendarEventTypeSelectArray[NumOfCalendarEvents-1];
	var CalPlaylistNameObj=CalendarPlaylistNameInputArray[NumOfCalendarEvents-1];
	if(CalPlaylistNameObj!=null && CalPlaylistNameObj!=undefined && CalEvtTypeObj!=null && CalEvtTypeObj!=undefined) {
		if(dji>-1) {
			PushUpdateKill();
			SetObjectValue(CalEvtTypeObj,"playlist");
			CallObjectChanged(CalEvtTypeObj);
			SetObjectValue(CalPlaylistNameObj,PMSymNameObj.value);
			CallObjectChanged(CalPlaylistNameObj);
			PopUpdateKill();
			DoUpdate();
			alert("The playlist has been added to the Calendar event list.  Double check the name matches and to set the desired run-time(s).");
		}
	}
}

function onPlaylistMultiSymNameInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=PlaylistMultiSymNameInputArray.indexOf(obj)
	if(i<0)return;
	var OldSymName=PlaylistMultiSymNameArray[i];
	PlaylistMultiSymNameArray[i]=(PlaylistMultiSymNameInputArray[i].value);
	SetObjCookie(PlaylistMultiSymNameInputArray[i]);
	var NewSymName=PlaylistMultiSymNameArray[i];
	if(OldSymName!=NewSymName) {
		for(var i=0;i<NumOfCalendarEvents;i++) {
			if(CalendarPlaylistNameArray[i]==OldSymName) {
				CalendarPlaylistNameArray[i]=NewSymName;
				SetObjectValue(CalendarPlaylistNameInputArray[i],NewSymName);
				SetObjCookie(CalendarPlaylistNameInputArray[i]);
				PushUpdateKill();
				CallObjectChanged(CalendarPlaylistNameInputArray[i]);
				PopUpdateKill();
			}
		}
		DoUpdate();
	}
}

function onPlaylistMultiFileInputChanged(w) {
	var evt=w || window.event;
	var obj=evt.target || evt.srcElement;
	var i=PlaylistMultiFileInputArray.indexOf(obj)
	if(i<0)return;
	PlaylistMultiFileArray[i]=(PlaylistMultiFileInputArray[i].value);
	SetObjCookie(PlaylistMultiFileInputArray[i]);
	DoUpdate();
}

function MultiPlaylistSpanUpdate() {
	delete PlaylistMultiRemoveButtonArray;
	delete PlaylistMultiSymNameInputArray;
	delete PlaylistMultiFileInputArray;
	delete PlaylistMultiCalendarAddButtonArray;
	var str="";
	MultiPlaylistSpan.innerHTML=str;
	PlaylistMultiRemoveButtonArray = new Array(NumOfPlaylists);
	PlaylistMultiSymNameInputArray = new Array(NumOfPlaylists);
	PlaylistMultiFileInputArray = new Array(NumOfPlaylists);
	PlaylistMultiCalendarAddButtonArray = new Array(NumOfPlaylists);
	for(var rindex=0;rindex<NumOfPlaylists;rindex++) {
		str+="<br/><br/>";
		str+="<table style=\"width:100%\">";

		var naid="PlaylistMulti"+(rindex)+"RemoveButton";
		str+="<tr><td class=\"ConfigTableDescTD\" style=\"width:50%\">";
		str+="<input name=\""+naid+"\" id=\""+naid+"\" type=\"button\" class=\"button\" value=\"Remove Playlist\"/></td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="PlaylistMulti"+(rindex)+"SymNameInput";
		str+="Symbolic name</td><td><input name=\""+naid+"\" id=\""+naid+"\"/>";
		str+="</td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="PlaylistMulti"+(rindex)+"FileInput";
		str+="Playlist file</td><td><input name=\""+naid+"\" id=\""+naid+"\"/>";
		str+="</td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="PlaylistMulti"+(rindex)+"CalendarAddButton";
		str+="<input name=\""+naid+"\" id=\""+naid+"\" type=\"button\" class=\"button\" value=\"Calendar\"/></td><td>Add playlist to the calendar.";
		str+="</td></tr>";

		str+="</table>";
	}

	MultiPlaylistSpan.innerHTML=str;
	for(var rindex=0;rindex<NumOfPlaylists;rindex++) {
		var vi;

		var naid="PlaylistMulti"+(rindex)+"RemoveButton";
		vi=$(naid);
		PlaylistMultiRemoveButtonArray[rindex]=vi;
		GetObjCookie(vi);
		AETFC(vi,onPlaylistMultiRemoveButtonClicked);

		var naid="PlaylistMulti"+(rindex)+"SymNameInput";
		vi=$(naid);
		PlaylistMultiSymNameInputArray[rindex]=vi;
		PlaylistMultiSymNameArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onPlaylistMultiSymNameInputChanged);

		var naid="PlaylistMulti"+(rindex)+"CalendarAddButton";
		vi=$(naid);
		PlaylistMultiCalendarAddButtonArray[rindex]=vi;
		GetObjCookie(vi);
		AETFC(vi,onPlaylistMultiCalendarAddButtonClicked);

		var naid="PlaylistMulti"+(rindex)+"FileInput";
		vi=$(naid);
		PlaylistMultiFileInputArray[rindex]=vi;
		PlaylistMultiFileArray[rindex]=GetObjCookie(vi);
		AETFC(vi,onPlaylistMultiFileInputChanged);
	}
}

function MultiPointSpanUpdate(nos) {
	delete EndPointNameInputArray;
	delete EndPointPathInputArray;
	delete EndPointMaxUserInputArray;
	delete EndPointTypeSelectArray;
	delete EndPointMP3QualitySelectArray;
	delete EndPointBitrateInputArray;
	delete EndPointSamplerateInputArray;
	delete EndPointNumchnsInputArray;
	delete EndPointAuthHashInputArray;
	delete EndPointPasswordInputArray;
	delete EndPointAdminPasswordInputArray;
	delete EndPointTitleInputArray;

	var str="";
	MultiPointSpan.innerHTML=str;
	var narf=nos;
	if(nos<=0){narf=1;}
	EndPointNameInputArray = new Array(narf);
	EndPointPathInputArray = new Array(narf);
	EndPointMaxUserInputArray = new Array(NumOfEndPoints);
	EndPointTypeSelectArray = new Array(narf);
	EndPointMP3QualitySelectArray = new Array(narf);
	EndPointBitrateInputArray = new Array(narf);
	EndPointSamplerateInputArray = new Array(narf);
	EndPointNumchnsInputArray = new Array(narf);
	EndPointAuthHashInputArray = new Array(narf);
	EndPointPasswordInputArray = new Array(narf);
	EndPointAdminPasswordInputArray = new Array(narf);
	EndPointTitleInputArray = new Array(narf);
	str+="<tr><td><table style=\"width:100%\">";

	for(var i=0;i<narf;i++) {
		var rindex=i+1;
		/*if(Mode==2) */{
			str+="<tr><td><br/></td></tr>";
			str+="<tr><td colspan=\"2\" class=\"titlespan\">Endpoint #"+rindex+"</td></tr>";
			str+="<tr><td>&nbsp;</td></tr>";
			str+="<tr><td class=\"ConfigTableDescTD\">";

			var naid="EndPoint"+(rindex)+"NameInput";
			str+="Name</td><td><input name=\""+naid+"\" id=\""+naid+"\"/>";
			str+="</td></tr><tr><td class=\"ConfigTableDescTD\">";

			naid="EndPoint"+(rindex)+"PasswordInput";
			str+="Password</td><td><input name=\""+naid+"\" id=\""+naid+"\"/><br/>";
			str+="</td></tr><tr><td class=\"ConfigTableDescTD\">";

			naid="EndPoint"+(rindex)+"AdminInput";
			str+="Admin password</td><td><input name=\""+naid+"\" id=\""+naid+"\"/><br/>";
			str+="</td></tr><tr><td class=\"ConfigTableDescTD\">";

			if(rindex!=1) {
				naid="EndPoint"+(rindex)+"TitleInput";
				str+="Title</td><td><input name=\""+naid+"\" id=\""+naid+"\"/><br/>";
				str+="</td></tr><tr><td class=\"ConfigTableDescTD\">";
			}

			naid="EndPoint"+(rindex)+"PathInput";
			str+="Path</td><td><input name=\""+naid+"\" id=\""+naid+"\"/><br/>";
			str+="</td></tr><tr><td class=\"ConfigTableDescTD\">";

			naid="EndPoint"+(rindex)+"MaxUserInput";
			str+="Maximum listeners</td><td><input name=\""+naid+"\" id=\""+naid+"\"/><br/>";
			str+="</td></tr><tr><td class=\"ConfigTableDescTD\">";

			naid="EndPoint"+(rindex)+"AuthHashInput";
			str+="Authhash</td><td><input name=\""+naid+"\" id=\""+naid+"\"/><br/>";
			str+="</td></tr>";
		}

		str+="<tr><td style=\"width:50%\" class=\"ConfigTableDescTD\">";
		var naid="EndPoint"+(rindex)+"BitrateInput";
		str+="Bitrate</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"96000\"/><br/></td></tr>";
		var naid="EndPoint"+(rindex)+"SamplerateInput";
		str+="<tr><td class=\"ConfigTableDescTD\">Samplerate</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"44100\"/><br/></td></tr>";
		var naid="EndPoint"+(rindex)+"NumchnsInput";
		str+="<tr><td class=\"ConfigTableDescTD\"># of channels</td><td><input name=\""+naid+"\" id=\""+naid+"\" value=\"2\"/><br/></td></tr>";

		str+="<tr><td class=\"ConfigTableDescTD\">";
		var naid="EndPoint"+(rindex)+"TypeSelect";
		str+="Encoder type</td><td><select style=\"width:136px;\" name=\""+naid+"\" id=\""+naid+"\"/><option value=\"aacp\">ADTS-AAC</option><option value=\"mp3\">MP3</option></select><br/></td></tr>";

		str+="<tr><td colspan=\"2\">";
		var naid="EndPoint"+(rindex)+"MP3FormatSpan";
		str+="<span style=\"width:100%\" name=\""+naid+"\" id=\""+naid+"\" style=\"visibility:hidden;display:none;\">";
		var naid="EndPoint"+(rindex)+"MP3QualitySelect";
		str+="<table colspan\"2\" style=\"width:100%\"><tr><td style=\"width:50%\" class=\"ConfigTableDescTD\">";
		str+="MP3 quality</td><td style=\"text-align:left\"><select style=\"width:136px;\" name=\""+naid+"\" id=\""+naid+"\"/><option value=\"0\">Fast</option><option value=\"1\">High Quality</option></select><br/>";
		str+="</td></tr></span></td></tr></table></td></tr>";
	}

	str+="</td></tr></table>";
	MultiPointSpan.innerHTML=str;
	for(var i=0;i<narf;i++) {
		var rindex=i+1;

		var naid;
		var vi;

		/*if(Mode==2) */{
			naid="EndPoint"+(rindex)+"NameInput";
			vi=$(naid);
			EndPointNameInputArray[i]=vi;
			vi.value="endpoint"+rindex;
			GetObjCookie(vi);
			AETFC(vi,onEndPointGenericObjectChanged);

			naid="EndPoint"+(rindex)+"PathInput";
			vi=$(naid);
			EndPointPathInputArray[i]=vi;
			vi.value="";
			GetObjCookie(vi);
			AETFC(vi,onEndPointGenericObjectChanged);

			naid="EndPoint"+(rindex)+"MaxUserInput";
			vi=$(naid);
			EndPointMaxUserInputArray[i]=vi;
			vi.value="";
			GetObjCookie(vi);
			AETFC(vi,onEndPointGenericObjectChanged);
		}

		naid="EndPoint"+(rindex)+"TypeSelect";
		vi=$(naid);
		EndPointTypeSelectArray[i]=vi;
		GetObjCookie(vi);
		DoEndPointTypeSelectUpdate(vi);
		AETFC(vi,onEndPointTypeSelectChanged);

		naid="EndPoint"+(rindex)+"MP3QualitySelect";
		vi=$(naid);
		EndPointMP3QualitySelectArray[i]=vi;
		GetObjCookie(vi);
		AETFC(vi,onEndPointGenericObjectChanged);

		naid="EndPoint"+(rindex)+"BitrateInput";
		vi=$(naid);
		EndPointBitrateInputArray[i]=vi;
		GetObjCookie(vi);
		AETFC(vi,onEndPointGenericObjectChanged);

		naid="EndPoint"+(rindex)+"SamplerateInput";
		vi=$(naid);
		EndPointSamplerateInputArray[i]=vi;
		GetObjCookie(vi);
		AETFC(vi,onEndPointGenericObjectChanged);

		naid="EndPoint"+(rindex)+"NumchnsInput";
		vi=$(naid);
		EndPointNumchnsInputArray[i]=vi;
		GetObjCookie(vi);
		AETFC(vi,onEndPointGenericObjectChanged);

		/*if(Mode==2) */{
			naid="EndPoint"+(rindex)+"AuthHashInput";
			vi=$(naid);
			EndPointAuthHashInputArray[i]=vi;
			GetObjCookie(vi);
			AETFC(vi,onEndPointGenericObjectChanged);

			naid="EndPoint"+(rindex)+"PasswordInput";
			vi=$(naid);
			EndPointPasswordInputArray[i]=vi;
			GetObjCookie(vi);
			AETFC(vi,onEndPointGenericObjectChanged);

			naid="EndPoint"+(rindex)+"AdminInput";
			vi=$(naid);
			EndPointAdminPasswordInputArray[i]=vi;
			GetObjCookie(vi);
			AETFC(vi,onEndPointGenericObjectChanged);

			if(rindex!=1) {
				naid="EndPoint"+(rindex)+"TitleInput";
				vi=$(naid);
				EndPointTitleInputArray[i]=vi;
				GetObjCookie(vi);
				AETFC(vi,onEndPointGenericObjectChanged);
			}
		}
	}
}

function DoBuilderViewModeUpdate(m) {
	DoObjShowHide(m, VUImagesHeader);
	DoObjShowHide(m, VUImagesTable);
	DoObjShowHide(m, YPHeader);
	DoObjShowHide(m, YPTable);
	DoObjShowHide(m, FlashPolicyHeader);
	DoObjShowHide(m, FlashPolicyTable);
	DoObjShowHide(m, MiscellaneousHeader);
	DoObjShowHide(m, MiscellaneousTable);
	DoObjShowHide(m, DNASAdminThemeHeader);
	DoObjShowHide(m, DNASAdminThemeTable);
	DoObjShowHide(m, LiveCaptureHeader);
	DoObjShowHide(m, LiveCaptureTable);
	DoObjShowHide(m, ReplayGainHeader);
	DoObjShowHide(m, ReplayGainTable);
	DoObjShowHide(m, CalendarHeader);
	DoObjShowHide(m, CalendarTable);
	DoObjShowHide(m, DJsHeader);
	DoObjShowHide(m, DJsTable);
	DoObjShowHide(m, AddPlaylistButton);
	DoObjShowHide(m, DJPortsBlock);
	DoObjShowHide(m, PlaylistsBlock);
	DoObjShowHide(m, MetadataPatternBlock);
	DoObjShowHide(m, NameLookupsBlock);
	DoObjShowHide(m, DJCipherInputBlock);
	DoObjShowHide(m, BindAddressBlock);
	DoObjShowHide(m, DNASConfigreWriteBlock);
	DoObjShowHide(m, TransConfigreWriteBlock);
	DoObjShowHide(m, IntroBackupHeader);
	DoObjShowHide(m, IntroBackupTable);
}

function onBuilderViewModeHiddenChanged() {
	var m=(BuilderViewModeHidden.value)*1;
	if(BuilderViewMode!=m) {
		BuilderViewMode=m;
		SetObjCookie(BuilderViewModeHidden);
		DoBuilderViewModeUpdate(m);
		DoShowHideSections(1);
		DoUpdate();
	}
}

function onBuilderViewModeSimpleRadioClicked() {
	BuilderViewModeHidden.value=(BuilderViewModeSimpleRadio.value)*1;
	CallObjectChanged(BuilderViewModeHidden);
}

function onBuilderViewModeAdvancedRadioClicked() {
	BuilderViewModeHidden.value=(BuilderViewModeAdvancedRadio.value)*1;
	CallObjectChanged(BuilderViewModeHidden);
}

function onGenerateMinimalCheckBoxChanged() {
	GenerateMinimal=GenerateMinimalCheckBox.checked;
	SetObjCookie(GenerateMinimalCheckBox);
	DoUpdate();
}

function onTranscoderInheritCheckBoxChanged() {
	TranscoderInherit=TranscoderInheritCheckBox.checked;
	SetObjCookie(TranscoderInheritCheckBox);
	DoUpdate();
}

function HelpTextError(message) {
	var GenErrMess="Configuration Builder was unable to load the help database.";
	HelperTextDBKeysArray.push("");
	HelperTextDBValuesArray.push(GenErrMess+"<br/><br/>"+message+".");
	uht(HelperTextDBValuesArray);
}

function HelpTextOk(HelperXMLHTTP) {
	var rt="";
	try {
		rt=HelperXMLHTTP.responseText;
	}
	catch(e) {
		HelperTextDBKeysArray.push("");
		HelperTextDBValuesArray.push(GenErrMess+"<br/><br/>"+e);
		uht(HelperTextDBValuesArray);
		return;
	}

	if(rt=="") {
		HelpTextError("Empty database");
		return;
	}
	rt=rt.split("\n");

	for(var i=0;i<rt.length;i++) {
		var tts=rt[i];
		if(tts.indexOf(";")==0)continue;
		if(tts.indexOf("//")==0)continue;
		if(tts=="\r\n")continue;
		if(tts=="\n")continue;
		if(tts=="\r")continue;
		tts=tts.split(",");
		HelperTextDBKeysArray.push(tts.shift());
		HelperTextDBValuesArray.push(tts.join(","));
	}
}

function LoadHelpTextDB() {
	var HelperXMLHTTP;
	HelperTextDBKeysArray = new Array();
	HelperTextDBValuesArray = new Array();

	if(window.XDomainRequest) {
		HelperXMLHTTP=new XDomainRequest();
	}else if(window.XMLHttpRequest){
		HelperXMLHTTP=new XMLHttpRequest();
	}else{
		HelperXMLHTTP=new ActiveXObject("Microsoft.XMLHTTP");
	}

	if(HelperXMLHTTP==null || HelperXMLHTTP==undefined) {
		HelpTextError("AJAX API not supported in this browser");
		return;
	}

	HelperXMLHTTP.open("GET","config_builder.txt",true);
	if(window.XDomainRequest) {
		HelperXMLHTTP.onerror=function(){
			HelpTextError("Empty database");
		}
		HelperXMLHTTP.onload=function(){
			HelpTextOk(HelperXMLHTTP);
		};
	} else {
		HelperXMLHTTP.onreadystatechange=function(){
			if(HelperXMLHTTP.readyState == null || HelperXMLHTTP.readyState==4 && HelperXMLHTTP.status==200){
				HelpTextOk(HelperXMLHTTP);
			} else {
				if(HelperXMLHTTP.readyState == null || HelperXMLHTTP.readyState==4){
					HelpTextError("Empty database");
				}
			}
		};
	}
	HelperXMLHTTP.send(null);
}

function ConfigFileReadData(f) {
	SetDefaults();
	PutAllSettings(f);
}

function ConfigFileRead(f) {
	ConfigFileReadData(f.target.result);
}

function IEAlt(obj) {
	var c=0;
	var fso=null;
	var l="";
	try {
		fso = new ActiveXObject("Scripting.FileSystemObject");
	}
	catch(e) {
		alert(e);
		return;
	}
	var f=obj.value;
	if(fso.FileExists(f)) {
		f=fso.OpenTextFile(f,1);
		while(!(f.AtEndOfStream)) {
			c++;
			var a=f.ReadAll();
			l+=a;
		}
		f.Close();
		ConfigFileReadData(l);
	}
}

function DoConfigFileRead(obj) {
	try {
		var reader = new FileReader();
		reader.onload = ConfigFileRead;
		reader.readAsText(obj.files[0]);
		return;
	}
	catch(e) {
		IEAlt(obj);
	}
}

function onUploadConfigButtonClicked() {
	DoConfigFileRead($("UploadConfigFile"));
}

function DoShowHideSections(simple) {
	for(var id=2;id<=CollapsedArrayNum;id++) {
		CollapsedArray[id-1]=readCookie("CollapsedArray"+id);
		if(id > 8 && id < 14 && simple) {
			if(!BuilderViewMode) {
				DoObjShowHide(false, $(id));
			} else {
				if(CollapsedArray[id-1]==0 || CollapsedArray[id-1]==null) {
					DoObjShowHide(true, $(id));
				}
			}
		} else if((CollapsedArray[id-1]==1)) {
			if(!simple) {
				toggle(id);
			}
		}
	}
}

function DoInit() {
	PushUpdateKill();
	SetDefaults();

	myScrollTable = $('myScrollTable');
	HelperTable = $('HelperTable');

	SCServLinesTextArea=$("SCServLinesTextArea");
	SCTransLinesTextArea=$("SCTransLinesTextArea");
	SCCalendarLinesTextArea=$("SCCalendarLinesTextArea");

	try{document.querySelector("#SCServLinesTextArea").spellcheck = false;}catch(e) {
		try{$("SCServLinesTextArea").spellcheck=false;}catch(e) {}
	}
	try{document.querySelector("#SCTransLinesTextArea").spellcheck = false;}catch(e) {
		try{$("SCTransLinesTextArea").spellcheck=false;}catch(e) {}
	}
	try{document.querySelector("#SCCalendarLinesTextArea").spellcheck = false;}catch(e) {
		try{$("SCCalendarLinesTextArea").spellcheck=false;}catch(e) {}
	}

	AIMInputBlock=$("AIMInputBlock");
	IRCInputBlock=$("IRCInputBlock");
	ICQInputBlock=$("ICQInputBlock");
	YPTableBlock=$("YPTableBlock");

	NumOfPlaylistsHidden=$("NumOfPlaylistsHidden");
	NumOfPlaylists=GetObjCookie(NumOfPlaylistsHidden)*1;
	AETFC(NumOfPlaylistsHidden,onNumOfPlaylistsHiddenChanged);

	MultiPlaylistSpan=$("MultiPlaylistSpan");
	MultiPlaylistSpanUpdate();

	AddPlaylistButton=$("AddPlaylistButton");
	AETFC(AddPlaylistButton,onAddPlaylistButtonClicked);

	MultiPointSpan=$("MultiPointSpan");
	DefaultAuthHashTR=$("DefaultAuthHashTR");
	NumOfEndPointsTR=$("NumOfEndPointsTR");

	PlatformSelect=$("PlatformSelect");
	Platform=GetObjCookie(PlatformSelect)*1;
	AETFC(PlatformSelect,onPlatformSelectChanged);

	ConfigSelect=$("ConfigSelect");
	Config=GetObjCookie(ConfigSelect)*1;
	AETFC(ConfigSelect,onConfigSelectChanged);

	DNASBasePathInput=$("DNASBasePathInput");
	DNASBasePath=GetObjCookie(DNASBasePathInput);
	AETFC(DNASBasePathInput,onDNASBasePathInputChanged);

	TranscoderBasePathInput=$("TranscoderBasePathInput");
	TranscoderBasePath=GetObjCookie(TranscoderBasePathInput);
	AETFC(TranscoderBasePathInput,onTranscoderBasePathInputChanged);

	DNASConfFileInput=$("DNASConfFileInput");
	DNASConfFile=GetObjCookie(DNASConfFileInput);
	AETFC(DNASConfFileInput,onDNASConfFileInputChanged);

	TransConfFileInput=$("TransConfFileInput");
	TransConfFile=GetObjCookie(TransConfFileInput);
	AETFC(TransConfFileInput,onTransConfFileInputChanged);

	BufferTypeSelect=$("BufferTypeSelect");
	BufferType=GetObjCookie(BufferTypeSelect)*1;
	AETFC(BufferTypeSelect,onBufferTypeSelectChanged);

	AdaptiveBufferSizeTR=$("AdaptiveBufferSizeTR");
	FixedBufferSizeTR=$("FixedBufferSizeTR");
	DoBufferTypeUpdate(BufferType);

	AdaptiveBufferSizeInput=$("AdaptiveBufferSizeInput");
	AdaptiveBufferSize=GetObjCookie(AdaptiveBufferSizeInput)*1;
	AETFC(AdaptiveBufferSizeInput,onAdaptiveBufferSizeInputChanged);

	FixedBufferSizeInput=$("FixedBufferSizeInput");
	FixedBufferSize=GetObjCookie(FixedBufferSizeInput)*1;
	AETFC(FixedBufferSizeInput,onFixedBufferSizeInputChanged);

	BufferHardLimitInput=$("BufferHardLimitInput");
	BufferHardLimit=GetObjCookie(BufferHardLimitInput)*1;
	AETFC(BufferHardLimitInput,onBufferHardLimitInputChanged);

	MaxHeaderLineSizeInput=$("MaxHeaderLineSizeInput");
	MaxHeaderLineSize=GetObjCookie(MaxHeaderLineSizeInput)*1;
	AETFC(MaxHeaderLineSizeInput,onMaxHeaderLineSizeInputChanged);

	MaxHeaderLineCountInput=$("MaxHeaderLineCountInput");
	MaxHeaderLineCount=GetObjCookie(MaxHeaderLineCountInput)*1;
	AETFC(MaxHeaderLineCountInput,onMaxHeaderLineCountInputChanged);

	NameLookupsCheckBox=$("NameLookupsCheckBox");
	NameLookups=GetObjCookie(NameLookupsCheckBox);
	AETFC(NameLookupsCheckBox,onNameLookupsCheckBoxClicked);

	/*ModeSelect=$("ModeSelect");
	Mode=GetObjCookie(ModeSelect)*1;
	AETFC(ModeSelect,onModeSelectChanged);*/

	EnableLoggingCheckBox=$("EnableLoggingCheckBox");
	EnableLogging=GetObjCookie(EnableLoggingCheckBox);
	AETFC(EnableLoggingCheckBox,onEnableLoggingCheckBoxClicked);

	ScreenlogCheckBox=$("ScreenlogCheckBox");
	Screenlog=GetObjCookie(ScreenlogCheckBox);
	AETFC(ScreenlogCheckBox,onScreenlogCheckBoxClicked);

	ClientConnectLogCheckBox=$("ClientConnectCheckBox");
	ClientConnectLog=GetObjCookie(ClientConnectLogCheckBox);
	AETFC(ClientConnectLogCheckBox,onClientConnectLogCheckBoxClicked);

	DNASLogFileInput=$("DNASLogFileInput");
	DNASLogFile=GetObjCookie(DNASLogFileInput);
	AETFC(DNASLogFileInput,onDNASLogFileInputChanged);

	TransLogFileInput=$("TransLogFileInput");
	TransLogFile=GetObjCookie(TransLogFileInput);
	AETFC(TransLogFileInput,onTransLogFileInputChanged);

	W3CLoggingCheckBox=$("W3CLoggingCheckBox");
	W3CLogging=GetObjCookie(W3CLoggingCheckBox);
	AETFC(W3CLoggingCheckBox,onW3CLoggingCheckBoxClicked);

	W3CLogFileInput=$("W3CLogFileInput");
	W3CLogFile=GetObjCookie(W3CLogFileInput);
	AETFC(W3CLogFileInput,onW3CLogFileInputChanged);

	WebClientDebugCheckBox=$("WebClientDebugCheckBox");
	WebClientDebug=GetObjCookie(WebClientDebugCheckBox);
	AETFC(WebClientDebugCheckBox,onWebClientDebugCheckBoxClicked);

	YP1DebugCheckBox=$("YP1DebugCheckBox");
	YP1Debug=GetObjCookie(YP1DebugCheckBox);
	AETFC(YP1DebugCheckBox,onYP1DebugCheckBoxClicked);

	YP2DebugCheckBox=$("YP2DebugCheckBox");
	YP2Debug=GetObjCookie(YP2DebugCheckBox);
	AETFC(YP2DebugCheckBox,onYP2DebugCheckBoxClicked);

	SHOUTcastSourceDebugCheckBox=$("SHOUTcastSourceDebugCheckBox");
	SHOUTcastSourceDebug=GetObjCookie(SHOUTcastSourceDebugCheckBox);
	AETFC(SHOUTcastSourceDebugCheckBox,onSHOUTcastSourceDebugCheckBoxClicked);

	UVOX2SourceDebugCheckBox=$("UVOX2SourceDebugCheckBox");
	UVOX2SourceDebug=GetObjCookie(UVOX2SourceDebugCheckBox);
	AETFC(UVOX2SourceDebugCheckBox,onUVOX2SourceDebugCheckBoxClicked);

	SHOUTcast1ClientDebugCheckBox=$("SHOUTcast1ClientDebugCheckBox");
	SHOUTcast1ClientDebug=GetObjCookie(SHOUTcast1ClientDebugCheckBox);
	AETFC(SHOUTcast1ClientDebugCheckBox,onSHOUTcast1ClientDebugCheckBoxClicked);

	SHOUTcast2ClientDebugCheckBox=$("SHOUTcast2ClientDebugCheckBox");
	SHOUTcast2ClientDebug=GetObjCookie(SHOUTcast2ClientDebugCheckBox);
	AETFC(SHOUTcast2ClientDebugCheckBox,onSHOUTcast2ClientDebugCheckBoxClicked);

	RelaySHOUTcastDebugCheckBox=$("RelaySHOUTcastDebugCheckBox");
	RelaySHOUTcastDebug=GetObjCookie(RelaySHOUTcastDebugCheckBox);
	AETFC(RelaySHOUTcastDebugCheckBox,onRelaySHOUTcastDebugCheckBoxClicked);

	RelayUVOXDebugCheckBox=$("RelayUVOXDebugCheckBox");
	RelayUVOXDebug=GetObjCookie(RelayUVOXDebugCheckBox);
	AETFC(RelayUVOXDebugCheckBox,onRelayUVOXDebugCheckBoxClicked);

	RelayDebugCheckBox=$("RelayDebugCheckBox");
	RelayDebug=GetObjCookie(RelayDebugCheckBox);
	AETFC(RelayDebugCheckBox,onRelayDebugCheckBoxClicked);

	StreamDataDebugCheckBox=$("StreamDataDebugCheckBox");
	StreamDataDebug=GetObjCookie(StreamDataDebugCheckBox);
	AETFC(StreamDataDebugCheckBox,onStreamDataDebugCheckBoxClicked);

	HTTPStyleDebugCheckBox=$("HTTPStyleDebugCheckBox");
	HTTPStyleDebug=GetObjCookie(HTTPStyleDebugCheckBox);
	AETFC(HTTPStyleDebugCheckBox,onHTTPStyleDebugCheckBoxClicked);

	StatsDebugCheckBox=$("StatsDebugCheckBox");
	StatsDebug=GetObjCookie(StatsDebugCheckBox);
	AETFC(StatsDebugCheckBox,onStatsDebugCheckBoxClicked);

	MicroServerDebugCheckBox=$("MicroServerDebugCheckBox");
	MicroServerDebug=GetObjCookie(MicroServerDebugCheckBox);
	AETFC(MicroServerDebugCheckBox,onMicroServerDebugCheckBoxClicked);

	ThreadRunnerDebugCheckBox=$("ThreadRunnerDebugCheckBox");
	ThreadRunnerDebug=GetObjCookie(ThreadRunnerDebugCheckBox);
	AETFC(ThreadRunnerDebugCheckBox,onThreadRunnerDebugCheckBoxClicked);

	RTMPClientDebugCheckBox=$("RTMPClientDebugCheckBox");
	RTMPClientDebug=GetObjCookie(RTMPClientDebugCheckBox);
	AETFC(RTMPClientDebugCheckBox,onRTMPClientDebugCheckBoxClicked);

	ShuffleDebugCheckBox=$("ShuffleDebugCheckBox");
	ShuffleDebug=GetObjCookie(ShuffleDebugCheckBox);
	AETFC(ShuffleDebugCheckBox,onShuffleDebugCheckBoxClicked);

	SHOUTcastDebugCheckBox=$("SHOUTcastDebugCheckBox");
	SHOUTcastDebug=GetObjCookie(SHOUTcastDebugCheckBox);
	AETFC(SHOUTcastDebugCheckBox,onSHOUTcastDebugCheckBoxClicked);

	UVOXDebugCheckBox=$("UVOXDebugCheckBox");
	UVOXDebug=GetObjCookie(UVOXDebugCheckBox);
	AETFC(UVOXDebugCheckBox,onUVOXDebugCheckBoxClicked);

	GainDebugCheckBox=$("GainDebugCheckBox");
	GainDebug=GetObjCookie(GainDebugCheckBox);
	AETFC(GainDebugCheckBox,onGainDebugCheckBoxClicked);

	PlaylistDebugCheckBox=$("PlaylistDebugCheckBox");
	PlaylistDebug=GetObjCookie(PlaylistDebugCheckBox);
	AETFC(PlaylistDebugCheckBox,onPlaylistDebugCheckBoxClicked);

	MP3EncDebugCheckBox=$("MP3EncDebugCheckBox");
	MP3EncDebug=GetObjCookie(MP3EncDebugCheckBox);
	AETFC(MP3EncDebugCheckBox,onMP3EncDebugCheckBoxClicked);

	MP3DecDebugCheckBox=$("MP3DecDebugCheckBox");
	MP3DecDebug=GetObjCookie(MP3DecDebugCheckBox);
	AETFC(MP3DecDebugCheckBox,onMP3DecDebugCheckBoxClicked);

	ResamplerDebugCheckBox=$("ResamplerDebugCheckBox");
	ResamplerDebug=GetObjCookie(ResamplerDebugCheckBox);
	AETFC(ResamplerDebugCheckBox,onResamplerDebugCheckBoxClicked);

	RGCalcDebugCheckBox=$("RGCalcDebugCheckBox");
	RGCalcDebug=GetObjCookie(RGCalcDebugCheckBox);
	AETFC(RGCalcDebugCheckBox,onRGCalcDebugCheckBoxClicked);

	APIDebugCheckBox=$("APIDebugCheckBox");
	APIDebug=GetObjCookie(APIDebugCheckBox);
	AETFC(APIDebugCheckBox,onAPIDebugCheckBoxClicked);

	CalendarDebugCheckBox=$("CalendarDebugCheckBox");
	CalendarDebug=GetObjCookie(CalendarDebugCheckBox);
	AETFC(CalendarDebugCheckBox,onCalendarDebugCheckBoxClicked);

	CaptureDebugCheckBox=$("CaptureDebugCheckBox");
	CaptureDebug=GetObjCookie(CaptureDebugCheckBox);
	AETFC(CaptureDebugCheckBox,onCaptureDebugCheckBoxClicked);

	DJDebugCheckBox=$("DJDebugCheckBox");
	DJDebug=GetObjCookie(DJDebugCheckBox);
	AETFC(DJDebugCheckBox,onDJDebugCheckBoxClicked);

	FlashPolicyServerDebugCheckBox=$("FlashPolicyServerDebugCheckBox");
	FlashPolicyServerDebug=GetObjCookie(FlashPolicyServerDebugCheckBox);
	AETFC(FlashPolicyServerDebugCheckBox,onFlashPolicyServerDebugCheckBoxClicked);

	FileConverterDebugCheckBox=$("FileConverterDebugCheckBox");
	FileConverterDebug=GetObjCookie(FileConverterDebugCheckBox);
	AETFC(FileConverterDebugCheckBox,onFileConverterDebugCheckBoxClicked);

	SourceRelayDebugCheckBox=$("SourceRelayDebugCheckBox");
	SourceRelayDebug=GetObjCookie(SourceRelayDebugCheckBox);
	AETFC(SourceRelayDebugCheckBox,onSourceRelayDebugCheckBoxClicked);

	SourceAndEndpointManagerDebugCheckBox=$("SourceAndEndpointManagerDebugCheckBox");
	SourceAndEndpointManagerDebug=GetObjCookie(SourceAndEndpointManagerDebugCheckBox);
	AETFC(SourceAndEndpointManagerDebugCheckBox,onSourceAndEndpointManagerDebugCheckBoxClicked);

	StreamTitleInput=$("StreamTitleInput");
	StreamTitle=GetObjCookie(StreamTitleInput);
	AETFC(StreamTitleInput,onStreamTitleInputChanged);

	StreamURLInput=$("StreamURLInput");
	StreamURL=GetObjCookie(StreamURLInput);
	AETFC(StreamURLInput,onStreamURLInputChanged);

	StreamGenreInput=$("StreamGenreInput");
	StreamGenre=GetObjCookie(StreamGenreInput);

	AIMInput=$("AIMInput");
	AIM=GetObjCookie(AIMInput);
	AETFC(AIMInput,onAIMInputChanged);

	IRCInput=$("IRCInput");
	IRC=GetObjCookie(IRCInput);
	AETFC(IRCInput,onIRCInputChanged);

	ICQInput=$("ICQInput");
	ICQ=GetObjCookie(ICQInput);
	AETFC(ICQInput,onICQInputChanged);

	UseMetadataCheckBox=$("UseMetadataCheckBox");
	UseMetadata=GetObjCookie(UseMetadataCheckBox);
	AETFC(UseMetadataCheckBox,onUseMetadataCheckBoxClicked);

	MetadataPatternInput=$("MetadataPatternInput");
	MetadataPattern=GetObjCookie(MetadataPatternInput);
	AETFC(MetadataPatternInput,onMetadataPatternInputChanged);

	DisplayMetadataPatternInput=$("DisplayMetadataPatternInput");
	DisplayMetadataPattern=GetObjCookie(DisplayMetadataPatternInput);
	AETFC(DisplayMetadataPatternInput,onDisplayMetadataPatternInputChanged);

	TitleFormatInput=$("TitleFormatInput");
	TitleFormat=GetObjCookie(TitleFormatInput);
	AETFC(TitleFormatInput,onTitleFormatInputChanged);

	URLFormatInput=$("URLFormatInput");
	URLFormat=GetObjCookie(URLFormatInput);
	AETFC(URLFormatInput,onURLFormatInputChanged);

	DNASPublicSelect=$("DNASPublicSelect");
	DNASPublic=GetObjCookie(DNASPublicSelect);
	AETFC(DNASPublicSelect,onDNASPublicSelectChanged);

	TransPublicCheckBox=$("TransPublicCheckBox");
	SCTransPublic=GetObjCookie(TransPublicCheckBox);
	AETFC(TransPublicCheckBox,onTransPublicCheckBoxClicked);

	MetaIntervalInput=$("MetaIntervalInput");
	MetaInterval=GetObjCookie(MetaIntervalInput)*1;
	AETFC(MetaIntervalInput,onMetaIntervalInputChanged);

	YPAddrInput=$("YPAddrInput");
	YPAddr=GetObjCookie(YPAddrInput);
	AETFC(YPAddrInput,onYPAddrInputChanged);

	YPPortInput=$("YPPortInput");
	YPPort=GetObjCookie(YPPortInput)*1;
	AETFC(YPPortInput,onYPPortInputChanged);

	YPPathInput=$("YPPathInput");
	YPPath=GetObjCookie(YPPathInput);
	AETFC(YPPathInput,onYPPathInputChanged);

	YPTimeoutInput=$("YPTimeoutInput");
	YPTimeout=GetObjCookie(YPTimeoutInput)*1;
	AETFC(YPTimeoutInput,onYPTimeoutInputChanged);

	YPMaxRetriesInput=$("YPMaxRetriesInput");
	YPMaxRetries=GetObjCookie(YPMaxRetriesInput)*1;
	AETFC(YPMaxRetriesInput,onYPMaxRetriesInputChanged);

	YPReportIntervalInput=$("YPReportIntervalInput");
	YPReportInterval=GetObjCookie(YPReportIntervalInput)*1;
	AETFC(YPReportIntervalInput,onYPReportIntervalInputChanged);

	YPMinReportIntervalInput=$("YPMinReportIntervalInput");
	YPMinReportInterval=GetObjCookie(YPMinReportIntervalInput)*1;
	AETFC(YPMinReportIntervalInput,onYPMinReportIntervalInputChanged);

	PortBaseInput=$("PortBaseInput");
	PortBase=GetObjCookie(PortBaseInput);
	AETFC(PortBaseInput,onPortBaseInputChanged);

	DNASIPInput=$("DNASIPInput");
	DNASIP=GetObjCookie(DNASIPInput);
	AETFC(DNASIPInput,onDNASIPInputChanged);

	RobotsTxtInput=$("RobotsTxtInput");
	RobotsTxt=GetObjCookie(RobotsTxtInput);
	AETFC(RobotsTxtInput,onRobotsTxtInputChanged);

	SourceBindAddressInput=$("SourceBindAddressInput");
	SourceBindAddress=GetObjCookie(SourceBindAddressInput);
	AETFC(SourceBindAddressInput,onSourceBindAddressInputChanged);

	DestinationBindAddressInput=$("DestinationBindAddressInput");
	DestinationBindAddress=GetObjCookie(DestinationBindAddressInput);
	AETFC(DestinationBindAddressInput,onDestinationBindAddressInputChanged);

	TransAdminPortInput=$("TransAdminPortInput");
	TransAdminPort=GetObjCookie(TransAdminPortInput)*1;
	AETFC(TransAdminPortInput,onTransAdminPortInputChanged);

	PasswordInput=$("PasswordInput");
	Password=GetObjCookie(PasswordInput);
	AETFC(PasswordInput,onPasswordInputChanged);

	AdminPasswordInput=$("AdminPasswordInput");
	AdminPassword=GetObjCookie(AdminPasswordInput);
	AETFC(AdminPasswordInput,onAdminPasswordInputChanged);

	VUImageDirectoryInput=$("VUImageDirectoryInput");
	VUImageDirectory=GetObjCookie(VUImageDirectoryInput);
	AETFC(VUImageDirectoryInput,onVUImageDirectoryInputChanged);

	VUImageSuffixInput=$("VUImageSuffixInput");
	VUImageSuffix=GetObjCookie(VUImageSuffixInput);
	AETFC(VUImageSuffixInput,onVUImageSuffixInputChanged);

	VUImageMimeTypeInput=$("VUImageMimeTypeInput");
	VUImageMimeType=GetObjCookie(VUImageMimeTypeInput);
	AETFC(VUImageMimeTypeInput,onVUImageMimeTypeInputChanged);

	DNASFlashPolicyFileInput=$("DNASFlashPolicyFileInput");
	DNASFlashPolicyFile=GetObjCookie(DNASFlashPolicyFileInput);
	AETFC(DNASFlashPolicyFileInput,onDNASFlashPolicyFileInputChanged);

	DNASFlashPolicyServerPortInput=$("DNASFlashPolicyServerPortInput");
	DNASFlashPolicyServerPort=GetObjCookie(DNASFlashPolicyServerPortInput)*1;
	AETFC(DNASFlashPolicyServerPortInput,onDNASFlashPolicyServerPortInputChanged);

	FlashPolicyFileInput=$("FlashPolicyFileInput");
	FlashPolicyFile=GetObjCookie(FlashPolicyFileInput);
	AETFC(FlashPolicyFileInput,onFlashPolicyFileInputChanged);

	FlashPolicyServerPortInput=$("FlashPolicyServerPortInput");
	FlashPolicyServerPort=GetObjCookie(FlashPolicyServerPortInput)*1;
	AETFC(FlashPolicyServerPortInput,onFlashPolicyServerPortInputChanged);

	MP3UnlockKeyNameInput=$("MP3UnlockKeyNameInput");
	MP3UnlockKeyName=GetObjCookie(MP3UnlockKeyNameInput);
	AETFC(MP3UnlockKeyNameInput,onMP3UnlockKeyNameInputChanged);

	MP3UnlockKeyCodeInput=$("MP3UnlockKeyCodeInput");
	MP3UnlockKeyCode=GetObjCookie(MP3UnlockKeyCodeInput);
	AETFC(MP3UnlockKeyCodeInput,onMP3UnlockKeyCodeInputChanged);

	DefaultAuthHashInput=$("DefaultAuthHashInput");
	DefaultAuthHash=GetObjCookie(DefaultAuthHashInput);
	AETFC(DefaultAuthHashInput,onDefaultAuthHashInputChanged);

	BanFileInput=$("BanFileInput");
	BanFile=GetObjCookie(BanFileInput);
	AETFC(BanFileInput,onBanFileInputChanged);

	RipFileInput=$("RipFileInput");
	RipFile=GetObjCookie(RipFileInput);
	AETFC(RipFileInput,onRipFileInputChanged);

	RipOnlyCheckBox=$("RipOnlyCheckBox");
	RipOnly=GetObjCookie(RipOnlyCheckBox);
	AETFC(RipOnlyCheckBox,onRipOnlyCheckBoxClicked);

	MaxListenersInput=$("MaxListenersInput");
	MaxListeners=GetObjCookie(MaxListenersInput)*1;
	AETFC(MaxListenersInput,onMaxListenersInputChanged);

	ListenerTimeInput=$("ListenerTimeInput");
	ListenerTime=GetObjCookie(ListenerTimeInput)*1;
	AETFC(ListenerTimeInput,onListenerTimeInputChanged);

	AutoDumpUsersCheckBox=$("AutoDumpUsersCheckBox");
	AutoDumpUsers=GetObjCookie(AutoDumpUsersCheckBox);
	AETFC(AutoDumpUsersCheckBox,onAutoDumpUsersCheckBoxClicked);

	CalendarEnableRewriteCheckBox=$("CalendarEnableRewriteCheckBox");
	CalendarEnableRewrite=GetObjCookie(CalendarEnableRewriteCheckBox);
	AETFC(CalendarEnableRewriteCheckBox,onCalendarEnableRewriteCheckBoxClicked);

	CalendarFileInput=$("CalendarFileInput");
	CalendarFile=GetObjCookie(CalendarFileInput);
	AETFC(CalendarFileInput,onCalendarFileInputChanged);

	PlaylistFileInput=$("PlaylistFileInput");
	PlaylistFile=GetObjCookie(PlaylistFileInput);
	AETFC(PlaylistFileInput,onPlaylistFileInputChanged);

	EnableShuffleCheckBox=$("EnableShuffleCheckBox");
	EnableShuffle=GetObjCookie(EnableShuffleCheckBox);
	AETFC(EnableShuffleCheckBox,onEnableShuffleCheckBoxClicked);

	XFadeTimeInput=$("XFadeTimeInput");
	XFadeTime=GetObjCookie(XFadeTimeInput);
	AETFC(XFadeTimeInput,onXFadeTimeInputChanged);

	XFadeThresholdInput=$("XFadeThresholdInput");
	XFadeThreshold=GetObjCookie(XFadeThresholdInput);
	AETFC(XFadeThresholdInput,onXFadeThresholdInputChanged);

	PlaylistFolderInput=$("PlaylistFolderInput");
	PlaylistFolder=GetObjCookie(PlaylistFolderInput);
	AETFC(PlaylistFolderInput,onPlaylistFolderInputChanged);

	PlaylistArchiveFolderInput=$("PlaylistArchiveFolderInput");
	PlaylistArchiveFolder=GetObjCookie(PlaylistArchiveFolderInput);
	AETFC(PlaylistArchiveFolderInput,onPlaylistArchiveFolderInputChanged);

	DJPortInput=$("DJPortInput");
	DJPort=GetObjCookie(DJPortInput)*1;
	AETFC(DJPortInput,onDJPortInputChanged);

	DJPort2Input=$("DJPort2Input");
	DJPort2=GetObjCookie(DJPort2Input)*1;
	AETFC(DJPort2Input,onDJPort2InputChanged);

	DJCipherInput=$("DJCipherInput");
	DJCipher=GetObjCookie(DJCipherInput);
	AETFC(DJCipherInput,onDJCipherInputChanged);

	DJAutoDumpSourceTimeInput=$("DJAutoDumpSourceTimeInput");
	DJAutoDumpSourceTime=GetObjCookie(DJAutoDumpSourceTimeInput)*1;
	AETFC(DJAutoDumpSourceTimeInput,onDJAutoDumpSourceTimeInputChanged);

	DJCaptureEnableCheckBox=$("DJCaptureEnableCheckBox");
	DJCaptureEnable=GetObjCookie(DJCaptureEnableCheckBox);
	AETFC(DJCaptureEnableCheckBox,onDJCaptureEnableCheckBoxClicked);

	DJBroadcastsPathInput=$("DJBroadcastsPathInput");
	DJBroadcastsPath=GetObjCookie(DJBroadcastsPathInput);
	AETFC(DJBroadcastsPathInput,onDJBroadcastsPathInputChanged);

	DJFilePatternInput=$("DJFilePatternInput");
	DJFilePattern=GetObjCookie(DJFilePatternInput);
	AETFC(DJFilePatternInput,onDJFilePatternInputChanged);

	MultiCalendarSpan=$("MultiCalendarSpan");
	NumOfCalendarEventsHidden=$("NumOfCalendarEventsHidden");
	NumOfCalendarEvents=GetObjCookie(NumOfCalendarEventsHidden)*1;
	if(NumOfCalendarEvents==null || NumOfCalendarEvents==NaN){NumOfCalendarEvents=0;NumOfCalendarEventsHidden.value=0;}
	AETFC(NumOfCalendarEventsHidden,onNumOfCalendarEventsHiddenChanged);
	MultiCalendarSpanUpdate();

	CalendarEventAddButton=$("CalendarEventAddButton");
	AETFC(CalendarEventAddButton,onCalendarEventAddButtonClicked);

	MultiDJSpan=$("MultiDJSpan");

	NumOfDJsHidden=$("NumOfDJsHidden");
	NumOfDJs=GetObjCookie(NumOfDJsHidden)*1;
	if(NumOfDJs==null || NumOfDJs==NaN){NumOfDJs=0;NumOfDJsHidden.value=0;}
	AETFC(NumOfDJsHidden,onNumOfDJsHiddenChanged);
	MultiDJSpanUpdate();

	DJAddButton=$("DJAddButton");
	AETFC(DJAddButton,onDJAddButtonClicked);

	EnableCaptureCheckBox=$("EnableCaptureCheckBox");
	EnableCapture=GetObjCookie(EnableCaptureCheckBox);
	AETFC(EnableCaptureCheckBox,onEnableCaptureCheckBoxClicked);

	CaptureDeviceInput=$("CaptureDeviceInput");
	CaptureDevice=GetObjCookie(CaptureDeviceInput);
	AETFC(CaptureDeviceInput,onCaptureDeviceInputChanged);

	CaptureInputInput=$("CaptureInputInput");
	CaptureInput=GetObjCookie(CaptureInputInput);
	AETFC(CaptureInputInput,onCaptureInputInputChanged);

	CaptureSampleRateInput=$("CaptureSampleRateInput");
	CaptureSampleRate=GetObjCookie(CaptureSampleRateInput);
	AETFC(CaptureSampleRateInput,onCaptureSampleRateInputChanged);

	CaptureNumChannelsInput=$("CaptureNumChannelsInput");
	CaptureNumChannels=GetObjCookie(CaptureNumChannelsInput);
	AETFC(CaptureNumChannelsInput,onCaptureNumChannelsInputChanged);

	ApplyReplayGainCheckBox=$("ApplyReplayGainCheckBox");
	ApplyReplayGain=GetObjCookie(ApplyReplayGainCheckBox);
	AETFC(ApplyReplayGainCheckBox,onApplyReplayGainCheckBoxClicked);

	DefaultReplayGainInput=$("DefaultReplayGainInput");
	DefaultReplayGain=GetObjCookie(DefaultReplayGainInput);
	AETFC(DefaultReplayGainInput,onDefaultReplayGainInputChanged);

	DJReplayGainInput=$("DJReplayGainInput");
	DJReplayGain=GetObjCookie(DJReplayGainInput);
	AETFC(DJReplayGainInput,onDJReplayGainInputChanged);

	CaptureReplayGainInput=$("CaptureReplayGainInput");
	CaptureReplayGain=GetObjCookie(CaptureReplayGainInput);
	AETFC(CaptureReplayGainInput,onCaptureReplayGainInputChanged);

	CalculateReplayGainCheckBox=$("CalculateReplayGainCheckBox");
	CalculateReplayGain=GetObjCookie(CalculateReplayGainCheckBox);
	AETFC(CalculateReplayGainCheckBox,onCalculateReplayGainCheckBoxClicked);

	ReplayGainTempFolderInput=$("ReplayGainTempFolderInput");
	ReplayGainTempFolder=GetObjCookie(ReplayGainTempFolderInput);
	AETFC(ReplayGainTempFolderInput,onReplayGainTempFolderInputChanged);

	ReplayGainRunAheadInput=$("ReplayGainRunAheadInput");
	ReplayGainRunAhead=GetObjCookie(ReplayGainRunAheadInput);
	AETFC(ReplayGainRunAheadInput,onReplayGainRunAheadInputChanged);

	ReplayGainDontWriteCheckBox=$("ReplayGainDontWriteCheckBox");
	ReplayGainDontWrite=GetObjCookie(ReplayGainDontWriteCheckBox);
	AETFC(ReplayGainDontWriteCheckBox,onReplayGainDontWriteCheckBoxClicked);

	EnhanceReplayGainInput=$("EnhanceReplayGainInput");
	EnhanceReplayGain=GetObjCookie(EnhanceReplayGainInput);
	AETFC(EnhanceReplayGainInput,onEnhanceReplayGainInputChanged);

	DNASConfigreWriteCheckBox=$("DNASConfigreWriteCheckBox");
	DNASConfigreWrite=GetObjCookie(DNASConfigreWriteCheckBox);
	AETFC(DNASConfigreWriteCheckBox,onDNASConfigreWriteCheckBoxClicked);

	TransConfigreWriteCheckBox=$("TransConfigreWriteCheckBox");
	TransConfigreWrite=GetObjCookie(TransConfigreWriteCheckBox);
	AETFC(TransConfigreWriteCheckBox,onTransConfigreWriteCheckBoxClicked);

	DNASAdminPageThemeInput=$("DNASAdminPageThemeInput");
	DNASAdminPageTheme=GetObjCookie(DNASAdminPageThemeInput);
	AETFC(DNASAdminPageThemeInput,onDNASAdminPageThemeInputChanged);

	DNASAdminPageFavIconInput=$("DNASAdminPageFavIconInput");
	DNASAdminPageFavIcon=GetObjCookie(DNASAdminPageFavIconInput);
	AETFC(DNASAdminPageFavIconInput,onDNASAdminPageFavIconInputChanged);

	DNASAdminPageFavIconMimeTypeInput=$("DNASAdminPageFavIconMimeTypeInput");
	DNASAdminPageFavIconMimeType=GetObjCookie(DNASAdminPageFavIconMimeTypeInput);
	AETFC(DNASAdminPageFavIconMimeTypeInput,onDNASAdminPageFavIconMimeTypeInputChanged);

	HideStatsCheckBox=$("HideStatsCheckBox");
	HideStats=GetObjCookie(HideStatsCheckBox);
	AETFC(HideStatsCheckBox,onHideStatsCheckBoxChanged);

	DNASIntroFileInput=$("DNASIntroFileInput");
	DNASIntroFile=GetObjCookie(DNASIntroFileInput);
	AETFC(DNASIntroFileInput,onDNASIntroFileInputChanged);

	DNASBackupFileInput=$("DNASBackupFileInput");
	DNASBackupFile=GetObjCookie(DNASBackupFileInput);
	AETFC(DNASBackupFileInput,onDNASBackupFileInputChanged);

	MaxSpecialFileSizeInput=$("MaxSpecialFileSizeInput");
	MaxSpecialFileSize=GetObjCookie(MaxSpecialFileSizeInput)*1;
	AETFC(MaxSpecialFileSizeInput,onMaxSpecialFileSizeInputChanged);

	ServerBackupFileInput=$("ServerBackupFileInput");
	ServerBackupFile=GetObjCookie(ServerBackupFileInput);
	AETFC(ServerBackupFileInput,onServerBackupFileInputChanged);

	ServerIntroFileInput=$("ServerIntroFileInput");
	ServerIntroFile=GetObjCookie(ServerIntroFileInput);
	AETFC(ServerIntroFileInput,onServerIntroFileInputChanged);

	SongHistoryInput=$("SongHistoryInput");
	SongHistory=GetObjCookie(SongHistoryInput);
	AETFC(SongHistoryInput,onSongHistoryInputChanged);

	ResetButton=$("ResetButton");
	AETFC(ResetButton,onResetButtonClicked);

	DownloadSCServButton=$("DownloadSCServButton");
	AETFC(DownloadSCServButton,onDownloadSCServButtonClicked);

	DownloadSCTransButton=$("DownloadSCTransButton");
	AETFC(DownloadSCTransButton,onDownloadSCTransButtonClicked);

	DownloadCalendarButton=$("DownloadCalendarButton");
	AETFC(DownloadCalendarButton,onDownloadCalendarButtonClicked);

	AETFC(SCServLinesTextArea,null);
	AETFC(SCTransLinesTextArea,null);
	AETFC(SCCalendarLinesTextArea,null);

	NumOfEndPointsInput=$("NumOfEndPointsInput");
	var w=GetObjCookie(NumOfEndPointsInput)*1;
	NumOfEndPointsInput.value=w;
	/*if(Mode*1==2)*/{NumOfEndPoints=w;}/*else{NumOfEndPoints=0;}*/
	AETFC(NumOfEndPointsInput,onNumOfEndPointsInputChanged);
	MultiPointSpanUpdate(NumOfEndPoints);
	//DoModeSelectUpdate(Mode);

	DoPlatformSelectUpdate(Platform);

	DoConfigSelectUpdate(Config);

	BuilderViewModeSimpleRadio=$("BuilderViewModeSimpleRadio");
	AETFC(BuilderViewModeSimpleRadio,onBuilderViewModeSimpleRadioClicked);

	BuilderViewModeAdvancedRadio=$("BuilderViewModeAdvancedRadio");
	AETFC(BuilderViewModeAdvancedRadio,onBuilderViewModeAdvancedRadioClicked);

	BuilderViewModeHidden=$("BuilderViewModeHidden");
	BuilderViewMode=GetObjCookie(BuilderViewModeHidden)*1;
	AETFC(BuilderViewModeHidden,onBuilderViewModeHiddenChanged);

	GenerateMinimalCheckBox=$("GenerateMinimalCheckBox");
	GenerateMinimal=GetObjCookie(GenerateMinimalCheckBox)*1;
	AETFC(GenerateMinimalCheckBox,onGenerateMinimalCheckBoxChanged);

	TranscoderInheritCheckBox=$("TranscoderInheritCheckBox");
	TranscoderInherit=GetObjCookie(TranscoderInheritCheckBox)*1;
	AETFC(TranscoderInheritCheckBox,onTranscoderInheritCheckBoxChanged);

	DNASDebugTable=$("DNASDebugTable");

	DNASDebugModeNoneRadio=$("DNASDebugModeNoneRadio");
	AETFC(DNASDebugModeNoneRadio,onDNASDebugModeNoneRadioClicked);

	DNASDebugModeAllRadio=$("DNASDebugModeAllRadio");
	AETFC(DNASDebugModeAllRadio,onDNASDebugModeAllRadioClicked);

	DNASDebugModeCustomRadio=$("DNASDebugModeCustomRadio");
	AETFC(DNASDebugModeCustomRadio,onDNASDebugModeCustomRadioClicked);

	DNASDebugModeHidden=$("DNASDebugModeHidden");
	DNASDebugMode=GetObjCookie(DNASDebugModeHidden)*1;
	DoDNASDebugModeUpdate(DNASDebugMode);
	AETFC(DNASDebugModeHidden,onDNASDebugModeHiddenChanged);

	TransDebugTable=$("TransDebugTable");

	TransDebugModeNoneRadio=$("TransDebugModeNoneRadio");
	AETFC(TransDebugModeNoneRadio,onTransDebugModeNoneRadioClicked);

	TransDebugModeAllRadio=$("TransDebugModeAllRadio");
	AETFC(TransDebugModeAllRadio,onTransDebugModeAllRadioClicked);

	TransDebugModeCustomRadio=$("TransDebugModeCustomRadio");
	AETFC(TransDebugModeCustomRadio,onTransDebugModeCustomRadioClicked);

	TransDebugModeHidden=$("TransDebugModeHidden");
	TransDebugMode=GetObjCookie(TransDebugModeHidden)*1;
	DoTransDebugModeUpdate(TransDebugMode);
	AETFC(TransDebugModeHidden,onTransDebugModeHiddenChanged);

	DoShowHideSections(0);

	VUImagesHeader=$("VUImagesHeader");
	VUImagesTable=$("VUImagesTable");
	YPHeader=$("YPHeader");
	YPTable=$("YPTable");
	FlashPolicyHeader=$("FlashPolicyHeader");
	FlashPolicyTable=$("FlashPolicyTable");
	MiscellaneousHeader=$("MiscellaneousHeader");
	MiscellaneousTable=$("MiscellaneousTable");
	DNASAdminThemeHeader=$("DNASAdminThemeHeader");
	DNASAdminThemeTable=$("DNASAdminThemeTable");
	LiveCaptureHeader=$("LiveCaptureHeader");
	LiveCaptureTable=$("LiveCaptureTable");
	ReplayGainHeader=$("ReplayGainHeader");
	ReplayGainTable=$("ReplayGainTable");
	CalendarHeader=$("CalendarHeader");
	CalendarTable=$("CalendarTable");
	DJsHeader=$("DJsHeader");
	DJsTable=$("DJsTable");
	DJPortsBlock=$("DJPortsBlock");
	PlaylistsBlock=$("PlaylistsBlock");
	MetadataPatternBlock=$("MetadataPatternBlock");
	NameLookupsBlock=$("NameLookupsBlock");
	DJCipherInputBlock=$("DJCipherInputBlock");
	BindAddressBlock=$("BindAddressBlock");
	DNASConfigreWriteBlock=$("DNASConfigreWriteBlock");
	TransConfigreWriteBlock=$("TransConfigreWriteBlock");
	IntroBackupHeader=$("IntroBackupHeader");
	IntroBackupTable=$("IntroBackupTable");
	CapatureDeviceBlock=$("CapatureDeviceBlock");
	DoObjShowHide(EnableCapture, CapatureDeviceBlock);

	PopUpdateKill();
	DoBuilderViewModeUpdate(BuilderViewMode);
	DoLoadGenres();
	DoUpdate();
	DoHelpUpdate(null);
}

function uht(t) {
	HelperSpan.innerHTML="<br/><div class='infh' align='center'>Help / Additional Information</div>"+t+"<br/><br/>";
}

// used for the genre lookups
function runUrlGet(urlString, callback){
	if (window.XMLHttpRequest){
		xmlhttp=new XMLHttpRequest();
	} else {
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}

	try {
		xmlhttp.open("GET","http://yp.shoutcast.com/"+urlString,false);
		xmlhttp.send(null);
		if (xmlhttp.readyState==4 && xmlhttp.status==200){
			callback();
		}
	} catch (e) {
	}
}

var lastPriGenre = "";
function getSecondaryGenre(){
	if ($("prigenre").value != lastPriGenre && $("prigenre").value != "Select"){
		runUrlGet("authutil_secondarygenre?primarygenre="+escape($("prigenre").value), getSecondaryGenreHandler);
	} else {
		if ($("prigenre").value == "Select"){
			$("SecondaryStreamGenre").innerHTML="";
		}
	}
	lastPriGenre = $("prigenre").value;
	if(lastPriGenre != "" && $("subgenres") != null) {
		$("subgenres").setAttribute("style","width:136px;");
		AETFC($("subgenres"), onStreamGenreDropdownChanged);
	}
	DoObjShowHide(($("SecondaryStreamGenre").innerHTML!=""), $("SecondaryStreamGenreText"));
}

function getPrimaryGenre() {
	$("PrimaryStreamGenre").innerHTML=xmlhttp.responseText;
	$("prigenre").remove(0);
}

function getSecondaryGenreHandler(){
	$('SecondaryStreamGenre').innerHTML=xmlhttp.responseText;
}

function DoLoadGenres() {
	runUrlGet("authutil_primarygenre", getPrimaryGenre);
	if($("prigenre") != null) {
		$("prigenre").setAttribute("style","width:136px;");
		$("prigenre").onkeyup=getSecondaryGenre;
		$("prigenre").onchange=getSecondaryGenre;

		if(StreamGenre != null && StreamGenre != "") {
			var urlString="http://yp.shoutcast.com/authutil_parentgenre?genre="+escape(StreamGenre);

			if (window.XMLHttpRequest){
				xmlhttp=new XMLHttpRequest();
			} else {
				xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
			}

			xmlhttp.open("GET",urlString,false);
			xmlhttp.send(null);
			if (xmlhttp.readyState==4 && xmlhttp.status==200){
				var primary = xmlhttp.responseText;
				if(primary == null || primary == ""){
					primary = StreamGenre;
				}
				setSelectOption($('prigenre'), primary);
				getSecondaryGenre();
				setSelectOption($('subgenres'), StreamGenre);
			}
		}

		DoObjShowHide(($("SecondaryStreamGenre").innerHTML!=""), $("SecondaryStreamGenreText"));
		AETFC($("prigenre"), onStreamGenreDropdownChanged);
	}
}

function GetSpecifiedGenre() {
	if($('prigenre') != null && $('prigenre').value == "Select"){
		StreamGenre = "";
	} else {
		StreamGenre = $('prigenre').value;
	}

	if($('prigenre') != null && $('prigenre').value == "Select" &&
	   $('subgenres') != null && $('subgenres').value == "Select"){
		StreamGenre = "";
	} else {
		if($('subgenres') != null){
			if($('subgenres').value == "Select"){
				StreamGenre = $('prigenre').value;
			}
			else{
				StreamGenre = $('subgenres').value;
			}
		}
	}
}

function setSelectOption(opts, value) {
	if(opts != null && opts.options != null) {
		for (var i = 0, optionsLength = opts.options.length; i < optionsLength; i++) {
			if (opts.options[i].value == value) {
				opts.selectedIndex = i;
				return true;
			}
		}
	}
	return false;
}

function toggle(id) {
	var visible = ($(id).style.visibility!="");
	DoObjShowHide(visible, $(id));
	if(!visible) {
		$(id+"I").src="expand.png";
	} else {
		$(id+"I").src="collapse.png";
	}
	CollapsedArray[id-1]=(!visible?1:0);
	createCookie("CollapsedArray"+id,(!visible?1:0));
}

window.onload=function(){
	HelperSpan=$("HelperSpan");
	LoadHelpTextDB();

	var UploadConfigFile=$("UploadConfigFile");
	var UploadConfigButton=$("UploadConfigButton");
	UploadConfigButton.onclick=onUploadConfigButtonClicked;
	UploadConfigFile.accept="text/plain";

	DoInit();
}