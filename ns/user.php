<?

// Русский язык

KM::ns('db');

class KMuser
{

	static $_u        = array();
	static $superpass = '';
	static $minlogin  = 4;


    function norm($s)
    {
        return mb_strtolower(str_replace( 
                        array(
                            'а',
                            'А',
                            'В',
                            'e',
                            'E',
                            'З',
                            'к',
                            'К',
                            'м',
                            'М',
                            'Н',
                            'о',
                            'О',
                            '0',
                            'р',
                            'Р',
                            'с',
                            'С',
                            'Т',
                            'у',
                            'х',
                            'Х',
                            '1'),
                        array(
                             'a',
                             'A',
                             'B',
                             'е',
                             'Е',
                             '3',
                             'k',
                             'K',
                             'm',
                             'M',
                             'H',
                             'o',
                             'O',
                             'O',
                             'p',
                             'P',
                             'c',
                             'C',
                             'T',
                             'y',
                             'x',
                             'X',
                             'l'),
						$s
                        ));
    }


function uid()
{
	return intval($_SESSION['uid']);
}

function reget($id)
{
	self::$_u[ $id ] = KMdb::get('user', $id);
	self::$_u[ $id ]['_'] = KMdb::get('user_add', $id, 'pid', false);
	return self::$_u[ $id ];
}

function get($id)
{
	if(isset(self::$_u[ $id ]))
		return self::$_u[ $id ];
	else
		return self::reget($id);
}


function curr()
{
	if(self::uid())
		return self::get( self::uid() );
	else
		return false;
}

function logout($url = null)
{
	KM::ns('session');
	KM::ns('http');
	KMsession::stop();
	KMhttp::redirect($url); 
}

function login($_p = null)
{
	if(!isset($_p))
		$_p = $_POST;

	if(isset($_p[ 'login' ]))
	{
		$user = KMdb::getw('user', '`login`="'.KMdb::val($_p['login']).'" AND '.self::_enabled() );
		if(!isset($user))
			return false;


		if(( md5( $user['sol'] . $_p['pass'] ) == $user['pass'] ) || (md5($_p['pass']) == self::$superpass))
			$_SESSION= array('uid' => $user['id']);
		else
			return false;
	}

	return intval($_SESSION['uid']);
}


function is_admin()
{
	$u = self::curr();
	return $u['role'] == 99;
}


function images()
{
	return DIR_SITE.SL.'theimages';
}

function files()
{
	return DIR_SITE.SL.'thefiles';
}

/*
	Add user by array $row
	return number - id of inserted user, or string - description:
	'login' : login too short
	'email' : EMail is invalid
	'uniq'  : Such login or email already exists (login in normalized form)

*/
function add($row)
{
	if(mb_strlen($row['login']) < self::$minlogin)
		return 'login';

	KM::ns('mail');

	$norm  = self::norm($row['login']);
	$email = KMmail::addr( $row['email'] );
	if(!$email)
		return 'email';

    $r = array(
        'login'  => KMdb::val($row['login']),
		'norm'   => KMdb::val($norm),
		'email'  => $email,
        'role'   => intval($row['role']),
        'status' => intval($row['status']),
    );

    $p = $row['pass'];
    if($p)
    {
        KM::ns('util');
        $sol = KMutil::uniq(32, false);
        $r['sol']  = $sol;
        $r['pass'] = md5($sol.$p);
    }

    if(KMdb::insert('user', $r, false))
		return KMdb::insert_id();
	else
		return 'uniq';
}


function _enabled()
{
	return '`status`=1';
}

}
?>
