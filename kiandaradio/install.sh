#!/usr/bin/env bash
# =============================================================================
#
# Centova Cast - Copyright 2010-2012, Centova Technologies Inc.
# Master installation script
#
# =============================================================================
HOMEPATH=/usr/local/centovacast

FILENAME=cc-bootstrap-latest.tar.gz
DATADIR=cc-bootstrap
UPDATEURL=http://install.centova.com
LICENSE_KEY=d2b804113573835396091d123722c0f5d53dbc87581f9a1f19c5f0bf385d178d

function show_help {
	for f in /usr/local/bin/bash /usr/bin/bash /bin/bash; do
		[ -f $f ] && BASHPATH=$f
	done

	[ -z "$BASHPATH" ] && BASHPATH="/usr/bin/env bash"

	cat <<EOF
Centova Cast Installer
======================

To download and prepare the installation script:
	wget -O install.sh install.centova.com/LICENSEKEY
	chmod 0755 install.sh

	Replace LICENSEKEY with your actual Centova Cast license key.

To perform a complete Centova Cast installation:
	./install.sh [options]

To install a cluster host (to be controlled by an existing web interface on
another server):
	./install.sh control <webip> <adminpass> [options]

	webip specifies the IP address of the web interface server, and adminpass
	specifies the admin password for the web interface server.

To perform a bare installation (advanced users only) run:
	./install.sh bare [options]

Available options:
  --help               show this help screen
  --channel=NAME       select channel to install (stable or unstable, default=stable)
  --vhosts=PATH        specify a custom path for all client data
  --force              overwrite any existing Centova Cast installation
  --deps               invoke yum/apt to install any missing package dependencies
						  before installing Centova Cast
  --shoutcast2         include ShoutCast DNAS v2 in installation
  --sctrans2           include sc_trans v2 in installation
  --icecast            include IceCast in installation
  --icescc             include ices-cc in installation
  --shoutcast-all      shorthand for --deps --shoutcast2 --sctrans2
  --icecast-all        shorthand for --deps --icecast --icescc

EOF

# not yet complete:
#  --homepath           specify an alternate installation root
#                         default: $HOMEPATH

	exit 1;	
}

function wget_print {
	while read line; do
		printf "%*.*s\r" -$COLS $COLS "${line//../}"

		case "$line" in
			*FAILED*) ERR=1
				;;
			*failed*) ERR=1
				;;
			*ERROR*) ERR=1
				;;
			*error*) ERR=1
				;;
			*) ERR=0
			;;
		esac
		[ $ERR -gt 0 ] && echo ""
	done

	return 0
}

function read_string {
	RSVARNAME="$1"
	RSDEFVALUE="$2"
	RSPROMPT="$3"
	RSARGS="-e "
	[ "$4" == "password" ] && RSARGS="$RSARGS -s"

	RSVNS="${RSVARNAME}_SET"
	if [ ! -z "${!RSVNS}" ]; then
		eval $RSVARNAME="${!RSVNS}"
		export $RSVARNAME
		return 0
	fi

	RSEMPTIES=0

	while [ true ]; do
		read -p "$RSPROMPT [$RSDEFVALUE]: " $RSARGS $RSVARNAME
		echo ""
		if [ -z "${!RSVARNAME}" ]; then
			if [ ! -z "$RSDEFVALUE" ]; then
				eval $RSVARNAME="$RSDEFVALUE"
				export $RSVARNAME
				return 0
			else
				RSEMPTIES=$((RSEMPTIES+1))
				[ $RSEMPTIES -gt 1 ] && echo "Aborted." && exit 1
				echo "You must enter a value.  If you want to abort this script, press ENTER one more time."
			fi
		else
			return 0
		fi
	done

}

function show_status {
	echo "$1"
}

function error_exit {
	echo "$1"
	exit 1
}

function get_file {
	COLS=$((COLUMNS-2))
	[ $COLS -lt 0 ] && COLS=78
	WGARGS=""
	[ $# -eq 2 ] && WGARGS="$WGARGS -O $2"
	WGARGS="$WGARGS $1"

	set -o pipefail
	wget --progress=dot --no-check-certificate $WGARGS 2>&1 | tee /tmp/cc_download.$$ | wget_print
	RES=$?

	if [ $RES -gt 0 ]; then
		echo "  > Download error details:"
		grep -v '\.\.\.\.\.\.' /tmp/cc_download.$$ | sed 's/^/  > /g'
	fi
	rm -f /tmp/cc_download.$$

	return $RES
}

function detect_os_arch {
	OSARCH=$(uname -m)
	LINUX64=0
	LINUX32=0

	BITS=$(getconf LONG_BIT)
	if [ "$BITS" == "32" ]; then
		LINUX32=1
	elif [ "$BITS" == "64" ]; then
		LINUX64=1
	elif [ "$OSARCH" == "x86_64" ]; then
		LINUX64=1
	else
		LINUX32=1
	fi

	export OSARCH LINUX32 LINUX64
}

function binexists {
	command -v "$1" > /dev/null 2>&1
	return $?
}

function randpass {
	local PWLEN=12 GENPW="" CCOUNT=0 CASC CORD CHAR ALNUM=1
	while [ $PWLEN -gt 0 ]; do
		read -N 1 CASC < /dev/urandom
		LC_CTYPE=C printf -v CORD "%d" "'$CASC"

		# alphanumeric passwords only?
		if [ $ALNUM -eq 0 ]; then
			# nope -- 33 .. 126 inclusive is our valid character range, so 94 characters
			CORD=$((CORD % 94))
			CORD=$((CORD + 33))
		else
			# yup, 48 .. 57, 65 .. 90, 97 .. 122 inclusive is our character range, so 62 characters
			CORD=$((CORD % 62))
			CORD=$((CORD + 48))
			if [ $CORD -gt 57 ] && [ $CORD -lt 65 ]; then
				CORD=$((CORD + 10))
			fi
			if [ $CORD -gt 90 ] && [ $CORD -lt 97 ]; then
				CORD=$((CORD + 26))
			fi
		fi
		printf -v CASC "%03o" "$CORD"
		printf -v CHAR \\"$CASC"
		GENPW="${CHAR}${GENPW}"

		PWLEN=$((PWLEN-1))
	done

	export "$1"="$GENPW"
}

function automate_web_install {
	[ -z "$DBHOST" ] && DBHOST="localhost"
	[ -z "$DBNAME" ] && DBNAME="centovacast"
	[ -z "$DBUSER" ] && DBUSER="centovacast"
	[ -z "$DBPASS" ] && randpass DBPASS

	local GRANTHOST="localhost"
	[ "$DBHOST" != "localhost" ] && GRANTHOST="%"

	if [ ! -z "$DBROOT" ]; then
		binexists mysql || ( echo "mysql client not found in path; cannot create database automatically" && return 1 )

		echo "CREATE DATABASE '${DBNAME}'; GRANT ALL PRIVILEGES ON '${DBNAME}'.* TO '${DBUSER}'@'${GRANTHOST}' IDENTIFIED BY '${DBPASS}'" | mysql -uroot -p"${DBROOT}" -h"${DBHOST}"
		[ $? -gt 0 ] && ( echo "failed to create database and grant privileges, cannot complete web installation automatically" && return 1 )
	fi

	echo "Performing automated web install ..."
	wget -q -O /tmp/cc_download.$$ "http://$WEBIP:2199/install.php" --post-data="settings[email]=${ADMINEMAIL}&settings[password1]=${ADMINPASS}&settings[password2]=${ADMINPASS}&settings[dbname]=${DBNAME}&settings[dbuser]=${DBUSER}&settings[dbpass]=${DBPASS}&settings[dbhost]=${DBHOST}&step=settings&install=1"
	[ $? -gt 0 ] && echo "HTTP request failed, cannot complete web installation automatically" && return 1

	return 0
}

function detect_web_ip {
	WEBIP=`ifconfig | grep 'inet addr:' | sed 's/^.*addr://g' | sed 's/ .*$//' | grep -vE -m 1 '^(10|127|192\.168|172\.16)\.'`
	[ -z "$WEBIP" ] && WEBIP=`ifconfig | grep 'inet addr:' | sed 's/^.*addr://g' | sed 's/ .*$//' | grep -vE -m 1 '^127\.'`
	[ -z "$WEBIP" ] && WEBIP="127.0.0.1"
}

INSTTYPE="$1"
[ -z "$INSTTYPE" ] && INSTTYPE="full"

if [ "$INSTTYPE" == "control" ]; then
	[ $# -lt 3 ] && show_help && exit 1

	shift
	WEBIP="$1"
	shift
	WEBPASS="$1"
	shift

	[ "${WEBIP:0:2}" == "--" ] && show_help && exit 1 
	[ "${WEBPASS:0:2}" == "--" ] && show_help && exit 1

	ARGS="--webip=$WEBIP --webpass=$WEBPASS --licensekey=$LICENSE_KEY $*"
else
	if [ "${INSTTYPE:0:2}" == "--" ]; then
		INSTTYPE="full"
	else
		shift
	fi
	ARGS="$*"
fi

BRANCH="stable"
VHOSTPATH=""
FORCE=0
FORCEARCH=""
FORCEDIST=""
INSTALLDEPS=0
EXTRAPACKAGES=""
while [ "$1" != "${1##[-+]}" ]; do
	case $1 in
		-h)
			show_help
			;;
		--help)
			show_help
			;;
#		--homepath=?*)
#			HOMEPATH=${1#--homepath=}
#			shift
#			;;
		--shoutcast-all)
			INSTALLDEPS=1
			EXTRAPACKAGES="$EXTRAPACKAGES shoutcast2 sctrans2"
			shift
			;;
		--icecast-all)
			INSTALLDEPS=1
			EXTRAPACKAGES="$EXTRAPACKAGES icecast icescc"
			shift
			;;
		--shoutcast)
			EXTRAPACKAGES="$EXTRAPACKAGES shoutcast2"
			shift
			;;
		--icescc|--icecast|--sctrans|--sctrans2|--shoutcast1|--shoutcast2)
			EXTRAPACKAGES="$EXTRAPACKAGES ${1#--}"
			shift
			;;
		--deps)
			INSTALLDEPS=1
			shift
			;;
		--channel=?*)
			BRANCH=${1##*=}
			shift
			;;
		--vhosts=?*)
			VHOSTPATH=${1##*=}
			shift
			;;
		--arch=?*)
			FORCEARCH=${1##*=}
			shift
			;;
		--dist=?*)
			FORCEDIST=${1##*=}
			shift
			;;
		--force)
			FORCE=1
			shift
			;;
		--admin-email=?*)
			ADMINEMAIL=${1##*=}
			shift
			;;
		--admin-pass=?*)
			ADMINPASS=${1##*=}
			shift
			;;
		--dbname=?*)
			DBNAME=${1##*=}
			shift
			;;
		--dbuser=?*)
			DBUSER=${1##*=}
			shift
			;;
		--dbpass=?*)
			DBPASS=${1##*=}
			shift
			;;
		--dbhost=?*)
			DBHOST=${1##*=}
			shift
			;;
		--dbroot=?*)
			DBROOT=${1##*=}
			shift
			;;
		--stroke-ego)
			echo "You smell nice today."
			exit 0
			;;
		*)
			shift
			;;
	esac
done

# find out who we're running as
MYUID=`id -u`
if [ $MYUID -ne 0 ]; then
	echo "This installer must be run as root."
	exit 1
fi

# are we FreeBSD? (todo: add support for other BSDs)
KERNOSTYPE=`uname -s`
if [ "$KERNOSTYPE" != "Linux" ]; then
	echo "This installer requires Linux."
	exit 1
fi


detect_os_arch
[ ! -z "$FORCEARCH" ] && OSARCH="$FORCEARCH"
case "$OSARCH" in
	i686|i1586|x86_64|amd64)
		if [ ! -z "$FORCEARCH" ]; then
			SYSTEMARCH="$FORCEARCH"
		elif [ $LINUX64 -gt 0 ]; then
			SYSTEMARCH="x86_64"
		else
			SYSTEMARCH="i686"
		fi
		;;
	i[345]86)
		echo "This installer requires an i686-class processor or better ($OSARCH not supported)."
		echo ""
		echo "If you are certain that your architecture is x86 or x86-64, you can override"
		echo "this check using --arch=i686 (for x86) or --arch=amd64 (for x86-64)".
		exit 1
		;;
	*)
		echo "Your machine type, $OSARCH, is not currently supported by this installer."
		echo ""
		echo "If you are certain that your architecture is x86 or x86-64, you can override"
		echo "this check using --arch=i686 (for x86) or --arch=amd64 (for x86-64)".
		exit 1
		;;
esac

if [ -f /etc/centovacast.conf -a $FORCE -eq 0 ]; then
	. /etc/centovacast.conf
	if [ ! -z "$HOMEPATH" -a -f "$HOMEPATH/etc/cc-services.conf" ]; then
		echo ""
		echo "/etc/centovacast.conf already exists.  Has Centova Cast already been installed?"
		echo "Use '--force' if you really mean it, but this will destroy any pre-existing"
		echo "installation if one exists."
		exit 1
	fi
fi

if [ ! -z "$VHOSTPATH" ]; then
	if [ ! -d "$VHOSTPATH" ]; then
		echo "The specified client data directory does not exist:"
		echo ""
		echo "    $VHOSTPATH"
		echo ""
		echo "You must create this directory before installation if you want to use it for"
		echo "client data."
		exit 1
	fi
fi

if [ $INSTALLDEPS -gt 0 ]; then

	clear
	echo ""
	echo ""
	echo "===== Centova Cast Installation =========================================="
	echo ""

	show_status "Installing package dependencies ..."

	DIST=""
	if [ ! -z "$FORCEDIST" ]; then
		DIST="$FORCEDIST"
	elif [ -f /etc/debian_version ]; then
		DIST="debian"
	elif [ -f /etc/redhat-release ]; then
		DIST="redhat"
	fi

	if [ -f /etc/lsb-release ]; then
		LSBDISTRIB=$(grep -m1 DISTRIB_ID ./etc/lsb-release | sed 's/^.*=//')
		if [ "$LSBDISTRIB" == "Ubuntu" ]; then
			echo ""
			echo "Ubuntu Linux detected; Ubuntu is not officially supported for use with Centova"
			echo "Cast.  Ubuntu-specific issues encountered in Centova Cast or related packages"
			echo -n "may not be supported by Centova Technologies."
			for i in 1 2 3 4 5; do echo -n " ."; sleep 1; done
			echo ""
			echo ""
		fi
	fi

	if [ "$DIST" == "debian" ]; then
		MULTIARCH=0

		if [ -f /etc/os-release ]; then
			VERSION_ID="0.0"
			. /etc/os-release
			VMAJOR=$(( ${VERSION_ID:0:1} + 0))
			[ $VMAJOR -gt 6 ] && MULTIARCH=1
		fi

		PACKAGES="findutils wget tar gzip unzip sed grep rsync build-essential debianutils icecast2"
		[ $INSTTYPE == "full" ] && PACKAGES="mysql-server $PACKAGES"
		if [ $SYSTEMARCH == "x86_64" ]; then
			if [ $MULTIARCH -gt 0 ]; then
				dpkg --add-architecture i386
				[ $? -gt 0 ] && echo "Centova Cast installer: dpkg refused to add i386 architecture (required for SHOUTcast), aborting" && exit 1
				apt-get update
				[ $? -gt 0 ] && echo "Centova Cast installer: apt-get update failed for i386 architecture (required for SHOUTcast), aborting" && exit 1

				PACKAGES="libc6:i386 $PACKAGES"
			else
				PACKAGES="ia32-libs $PACKAGES"
			fi
		fi
		apt-get install $PACKAGES
		[ $? -gt 0 ] && echo "Centova Cast installer: apt-get exited with an error, aborting" && exit 1

	elif [ "$DIST" == "redhat" ]; then
		PACKAGES="findutils wget tar gzip unzip sed grep gawk rsync gcc gcc-c++ make which"
		[ $INSTTYPE == "full" ] && PACKAGES="mysql-server $PACKAGES"
		[ $SYSTEMARCH == "x86_64" ] && PACKAGES="compat-glibc $PACKAGES"
		yum install $PACKAGES
		[ $? -gt 0 ] && echo "Centova Cast installer: yum exited with an error, aborting" && exit 1

	else
		echo "Unable to install package dependencies; cannot identify system as Red Hat or"
		echo "Debian based."
		echo ""
		echo "If you know your OS is based on Red Hat or Debian Linux, you can override this"
		echo "check by passing --dist=redhat (for Red Hat derivatives) or --dist=debian (for"
		echo "Debian Linux derivatives)."
		exit 1
	fi
fi

clear
echo ""
echo ""
echo "===== Centova Cast Installation =========================================="
echo ""

show_status "Downloading installation system ..."

UPDATETMP=$HOMEPATH/var/tmp/update
[ ! -d $UPDATETMP ] && mkdir -p $UPDATETMP

get_file $UPDATEURL/$BRANCH/$LICENSE_KEY/$SYSTEMARCH/$FILENAME $UPDATETMP/$FILENAME
[ $? -gt 0 ] && error_exit "Error downloading archive"
[ ! -f $UPDATETMP/$FILENAME ] && error_exit "Archive could not be saved"

HASHFILE=$UPDATETMP/${FILENAME}.md5
get_file ${UPDATEURL}/$BRANCH/$LICENSE_KEY/$SYSTEMARCH/$FILENAME.md5 $HASHFILE
[ $? -gt 0 ] && error_exit "Error downloading verification hash"
[ ! -f $HASHFILE ] && error_exit "Verification hash could not be saved"

echo "Verifying archive integrity ..."
cd $UPDATETMP
md5sum $HASHFILE >/dev/null 2>&1
[ $? -gt 0 ] && error_exit "Error: archive is incomplete or has been tampered with"

show_status "Unpacking archive ..."
tar xzf $FILENAME
[ ! -d $DATADIR ] && error_exit "Error: upacked archive does not contain data files"
[ ! -f install.sh ] && error_exit "Error: unpacked archive does not contain installer"

show_status "Beginning installation ..."

echo "HOMEPATH=$HOMEPATH" > /etc/centovacast.conf

chmod 0755 ./install.sh
./install.sh "$INSTTYPE" "$LICENSE_KEY" "$UPDATEURL" "$BRANCH" $ARGS

RC=$?

if [ $RC -eq 0 ]; then
	cd /
	EXTRAERRORS=""
	if [ ! -z "$EXTRAPACKAGES" ]; then
		echo ""
		echo ""
		echo "===== Supplementary Software Installation ================================"
		echo ""
		for f in $EXTRAPACKAGES; do
			show_status "Installing $f ..."
			$HOMEPATH/sbin/update --add $f
			if [ $? -gt 0 ]; then
				EXTRAERRORS="$EXTRAERRORS\n- $f did not install successfully and will NOT be available for use\n  To try again, run: $HOMEPATH/sbin/update --add $f"
			fi
		done
	fi

	rm -rf $UPDATETMP

	echo ""
	echo ""
	echo "===== Installation Complete =============================================="
	echo ""
	if [ "$INSTTYPE" == "full" ]; then
		detect_web_ip

		WEBINSTALLED=0
		if [ "$ADMINEMAIL" != "" ]; then
			automate_web_install
			[ $? -eq 0 ] && WEBINSTALLED=1
		fi

		if [ $WEBINSTALLED -gt 0 ]; then
			echo "The web interface may now be launched at:"
		else
			echo "To complete your installation, please launch the web interface at:"
		fi

		echo "http://${WEBIP}:2199/"
		echo ""
		echo "You can also access the interface via SSL at: https://${WEBIP}:2199/"

	elif [ "$INSTTYPE" == "bare" ]; then
		echo "The basic installation system has now been installed.  To add further"
		echo "packages run: /usr/local/centovacast/sbin/update --add <packagename>"

	else

		echo "This server has been added to your Centova Cast control panel.  To begin"
		echo "provisioning accounts to this server, please log in to your control panel"
		echo "now."

	fi
	echo ""

	[ ! -z "$EXTRAERRORS" ] && echo -e "NOTE:\n$EXTRAERRORS\n"

	echo "Thank you for using Centova Cast."
else
	echo "Installer exited with error, aborting"
	exit 1
fi
