<?php

class Server {

    public $dir = '/usr/home/mt2';

    private $connect;

    /**
     * Server constructor.
     * @param string $ip
     * @param string $user
     * @param string $pass
     * @param int $port
     */
    public function __construct(string $ip, string $user, string $pass, int $port = 22)
    {
        $this->connect = ssh2_connect($ip, $port);
        ssh2_auth_password($this->connect, $user, $pass);
    }

    /**
     * @param string $command
     * @return string
     */
    private function exec(string $command): string
    {
        $stream = ssh2_exec($this->connect, $command);
        stream_set_blocking($stream, true);

        $outPut = stream_get_contents(ssh2_fetch_stream($stream, SSH2_STREAM_STDIO));
        $error  = stream_get_contents(ssh2_fetch_stream($stream, SSH2_STREAM_STDERR));

        if(is_string($error) and strlen($error) > 0){
            return $error;
        }else{
            return $outPut;
        }
    }

    /**
     * @return array
     */
    public function status(): array
    {
        $status = $this->exec("sh /{$this->dir}/status.sh");
        $status = json_decode($status, true);
        return $status;
    }

    /**
     * @return bool
     */
    public function start(): bool
    {
        return $this->exec("sh /{$this->dir}/start.sh") ?? false;
    }

    /**
     * @return bool
     */
    public function stop(): bool
    {
        return $this->exec("sh /{$this->dir}/stop.sh") ?? false;
    }

    /**
     * @return bool
     */
    public function restart(): bool
    {
        $this->stop();
        sleep(1);
        return $this->start();
    }

    public function secondsToTime(int $seconds = 0): string
    {
        if($seconds){
            $dtF = new \DateTime('@0');
            $dtT = new \DateTime("@$seconds");

            $d = $dtF->diff($dtT)->format('%ad');
            $h = $dtF->diff($dtT)->format('%hh');
            $m = $dtF->diff($dtT)->format('%im');

            if($d != 0 and $h and $m){
                return $d.' '.$h.' '.$m;
            }elseif($h != 0 and $m){
                return $h.' '.$m;
            }elseif($m){
                return $m;
            }
        }else return false;
    }
}


$server = new Server('IP','USER','PASS');
$status = $server->status();

switch ($_POST['action']){
    case 'start':
        $server->start();
        break;

    case 'stop':
        $server->stop();
        break;

    case 'restart':
        $server->restart();
        break;
}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title>Server status</title>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-10">
                <h2>channel</h2>
                <table class="table text-center">
                    <thead>
                    <tr>
                        <th scope="col">name</th>
                        <th scope="col">online</th>
                        <th scope="col">time</th>
                        <th scope="col">status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>auth</td>
                        <td>-</td>
                        <td><?= $server->secondsToTime($status['auth']['time']) ?></td>
                        <td><?PHP if($status['auth']['pid']) echo 'online'; else echo 'offline'; ?></td>
                    </tr>
                    <tr>
                        <td>db</td>
                        <td>-</td>
                        <td><?= $server->secondsToTime($status['db']['time']) ?></td>
                        <td><?PHP if($status['db']['pid']) echo 'online'; else echo 'offline'; ?></td>
                    </tr>
                    <?PHP
                    foreach ($status['channels'] as $r){
                        $status = $r['pid'] ? 'online' : 'offline';
                        echo "
                    <tr>
                        <td>{$r['name']}</td>
                        <td>{$r['online']}</td>
                        <td>{$server->secondsToTime($r['time'])}</td>
                        <td>$status</td>
                    </tr>
                        ";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="col-2">
                <h2>action</h2>
                <form method="post">
                    <div class="list-group text-center">
                        <button type="submit" name="action" value="start" class="list-group-item list-group-item-action">start</button>
                        <button type="submit" name="action" value="stop" class="list-group-item list-group-item-action">stop</button>
                        <button type="submit" name="action" value="restart" class="list-group-item list-group-item-action">restart</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</body>
</html>
