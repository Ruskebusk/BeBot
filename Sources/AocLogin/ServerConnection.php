<?php

include_once "BinaryStream.php";
include_once "Endpoints.php";
include_once "protocolbuf/message/pb_message.php";

// This file is autogenerated by protoc_endpoints.php
// The template comes from Endpoints.proto
include_once "pb_proto_Endpoints.php";

define('LOGIN_TYPE_ENDPOINTS', 1);
define('LOGIN_TYPE_PROTOBUF', 2);

class ServerConnection
{
    protected $m_LogName;
    protected $m_EndpointType;

    private $m_ServerAddress;
    private $m_ServerPort;

    private $m_ClientEndpointName;
    private $m_ClientEndpointID;
    private $m_ServerEndpointID;
    private $m_ServerEndpointName;

    private $m_Socket;


    public function ServerConnection(
        $logname,
        $serverAddress,
        $serverPort,
        $clientEndpoint,
        $serverEndpoint,
        $endpointType
    ) {
        $this->m_LogName = $logname;

        $this->m_ServerAddress = $serverAddress;
        $this->m_ServerPort = $serverPort;

        $this->m_EndpointType = $endpointType;
        $this->m_ClientEndpoint = $clientEndpoint;
        $this->m_ServerEndpoint = $serverEndpoint;
    }


    public function Connect()
    {
        if (is_resource($this->m_Socket)) {
            socket_close($this->m_Socket);
        }

        $this->m_Socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if (!is_resource($this->m_Socket)) {
            echo("[" . $this->m_LogName . "] Could not create a socket\n");
            return false;
        }

        if (@socket_connect($this->m_Socket, $this->m_ServerAddress, $this->m_ServerPort) === false) {
            socket_close($this->m_Socket);
            $this->m_Socket = null;
            echo("[" . $this->m_LogName . "] Could not connect to " . $this->m_ServerAddress . ":" . $this->m_ServerPort . "\n");
            return false;
        }

        echo("[" . $this->m_LogName . "] Connected to " . $this->m_ServerAddress . ":" . $this->m_ServerPort . "\n");

        return true;
    }


    /// Disconnect
    /// Disconnect this connection from the server
    /// @string reason [in] The reason we are disconnecting from the server
    /// @author Chaoz
    public function Disconnect($reason)
    {
        if (is_resource($this->m_Socket)) {
            echo("[" . $this->m_LogName . "] Disconnected. [$reason]\n");
            socket_close($this->m_Socket);
        }
    }


    /// GetEndpointType
    /// Returns the endpoint login protocol ( standard / protocol buffers ) we are using
    /// @return uint16 [return] What type of endpoint login protocol we are using
    /// @author Chaoz
    public function GetEndpointType()
    {
        return $this->m_EndpointType;
    }


    /// HandlePackets
    /// Handles all incoming messages
    /// @author Chaoz
    protected function HandlePackets()
    {
        // Read the header
        $headerStream = $this->ReadNetworkData(4);
        if ($headerStream == null) {
            return null;
        }
        $packetsize = $headerStream->ReadUInt32();

        // Read the data
        $stream = $this->ReadNetworkData($packetsize);

        //echo("[" . $this->m_LogName . "] Received data [expected len:$packetsize] vs [received len:" . $stream->GetLength() . "\n");
        return $stream;
    }


    protected function GetRpcID($stream)
    {
        $rpcID = -1;
        if ($this->m_EndpointType == LOGIN_TYPE_ENDPOINTS) {
            $stream->ReadUInt32(); // Skip CRC
            $stream->ReadString(); // Skip caller Name
            $stream->ReadUInt32(); // Skip caller ID
            $stream->ReadUInt32(); // Skip caller type
            $stream->ReadString(); // Skip sender Name
            $stream->ReadUInt32(); // Skip sender ID
            $stream->ReadUInt32(); // Skip sender type

            $rpcID = $stream->ReadUInt32();
        } else {
            if ($this->m_EndpointType == LOGIN_TYPE_PROTOBUF) {
                $stream->ReadUInt32(); // Skip CRC

                $size = $stream->ReadUInt32();
                $raw = $stream->ReadRaw($size);

                //ServerConnection::hexdump("[header::in]",$raw, $size );

                $header = new RpcHeader();
                $header->parseFromString($raw);

                $rpcID = $header->method_instance();

                if (isset($rpcID) == false) {
                    $rpcID = -1;
                }
            }
        }
        //echo("[" . $this->m_LogName . "] Received data [len:" . $stream->GetLength() . "] RpcID: " . $rpcID . "\n");

        return $rpcID;
    }


    protected function CreateBinaryStream($rpcID)
    {
        $stream = new BinaryStream();

        if ($this->m_EndpointType == LOGIN_TYPE_ENDPOINTS) {
            $stream->WriteString($this->m_ClientEndpoint->GetName());
            $stream->WriteUInt32($this->m_ClientEndpoint->GetInstance());
            $stream->WriteUInt32($this->m_ClientEndpoint->GetToken());

            $stream->WriteString($this->m_ServerEndpoint->GetName());
            $stream->WriteUInt32($this->m_ServerEndpoint->GetInstance());
            $stream->WriteUInt32($this->m_ServerEndpoint->GetToken());

            $stream->WriteUInt32($rpcID);
        } else {
            if ($this->m_EndpointType == LOGIN_TYPE_PROTOBUF) {
                $header = new RpcHeader();

                $callerID = new ProtoEndpoint();
                $callerID->set_type($this->m_ClientEndpoint->GetType());
                if ($this->m_ClientEndpoint->GetInstance() != 0) {
                    $callerID->set_instance($this->m_ClientEndpoint->GetInstance());
                }
                if ($this->m_ClientEndpoint->GetToken() != 0) {
                    $callerID->set_token($this->m_ClientEndpoint->GetToken());
                }

                $endpointID = new ProtoEndpoint();
                $endpointID->set_type($this->m_ServerEndpoint->GetType());
                if ($this->m_ServerEndpoint->GetInstance() != 0) {
                    $endpointID->set_instance($this->m_ServerEndpoint->GetInstance());
                }
                if ($this->m_ServerEndpoint->GetToken() != 0) {
                    $endpointID->set_token($this->m_ServerEndpoint->GetToken());
                }

                $header->set_caller_id($callerID);
                $header->set_endpoint_id($endpointID);
                $header->set_method_instance($rpcID);

                $data = $header->SerializeToString();

                $len = strlen($data);
                $stream->WriteUInt32($len);
                $stream->WriteRaw($data, $len);
                //$this->hexdump( "[header::out]", $data, $len  );
            } else {
                die("CreateBinaryStream::Illegal endpoint type!");
            }
        }
        return $stream;
    }


    protected function EncryptAndSend($data, $rpcID)
    {
        $stream = new BinaryStream();

        $tst = substr($data->GetWriteData(), 0, $data->GetWriteLength());
        $crc = crc32($tst);

        // Write the length and CRC for the packet
        $stream->WriteUInt32($data->GetWriteLength() + 4);
        $stream->WriteUInt32($crc);

        // Write the actual datablob
        $stream->WriteRaw($data->GetWriteData(), $data->GetWriteLength());

        // Write the data out to the socket
        socket_write($this->m_Socket, $stream->GetWriteData(), $stream->GetWriteLength());

        //echo("[" . $this->m_LogName . "] Sending data  [len:" . $stream->GetWriteLength() . "] RpcID: $rpcID \n");
        //$this->hexdump( "[Binary Send : " . $rpcID . " ]", $stream->GetRawData(), $stream->GetWriteLength() );
    }


    protected function ReadNetworkData($len)
    {
        $data = "";
        $rlen = $len;

        while ($rlen > 0) {
            if (($tmp = socket_read($this->m_Socket, $rlen)) === false) {
                if (!is_resource($this->m_Socket)) {
                    $this->Disconnect("Socket read error");
                    die("Read error: $last_error\n");
                } else {
                    printf("Read error: %s\n", socket_strerror(socket_last_error($this->m_Socket)));
                    return null;
                }
            }

            if ($tmp == "") {
                $this->Disconnect("Socket EOF error");
                echo("Read error: EOF\n");

                if (!is_resource($this->m_Socket)) {
                    die("Read error: Too many EOF errors, disconnecting.\n");
                } else {
                    return null;
                }
            }

            $data .= $tmp;
            $rlen -= strlen($tmp);
        }

        $binaryData = new BinaryStream($data, $len);
        //$this->hexdump( "[EndpointData::ReadNetworkData]", $binaryData->GetRawData(), $binaryData->GetLength() );
        return $binaryData;
    }


    static function hexdump($prefix, $data, $len)
    {
        $rows = $len / 20;
        if ($rows > 50) {
            $rows = 50;
        }

        if ($rows > 0) {
            echo "\n";
            echo $prefix . " Length : " . $len . "\n";
        }

        for ($j = 0; $j < $rows; $j++) {
            echo $prefix . " ";

            $j1 = $j * 20;
            $j2 = ($j * 20) + 20;

            $strRow = "";

            for ($i = $j1; $i < $j2; $i++) {
                if ($i < $len) {
                    $temparray = unpack("C", (string)$data[$i + $offset]);
                    $byte = array_pop($temparray);

                    //if ( $byte < 256 ){ echo "0"; }
                    if ($byte < 16) {
                        echo "0";
                    }

                    printf("%X ", $byte);

                    if ($byte > 32 && $byte < 123) {
                        $strRow = $strRow . $data[$i];
                    } else {
                        $strRow = $strRow . ".";
                    }
                } else {
                    echo "-- ";
                    $strRow .= "-";
                }
            }

            echo " '$strRow'\n";
        }

        if ($rows > 0) {
            echo "\n";
        }
    }


    function displayConanError($errorcode)
    {
        $err = "Unknown";
        switch ($errorcode) {
            case 0:
                $err = "Login OK";
            case 1:
                $err = "Login timed out";
                break;
            case 2:
                $err = "Dimension is down";
                break;
            case 3:
                $err = "Too many characters logged in";
                break;
            case 4:
                $err = "Invalid characterslot";
                break;
            case 5:
                $err = "No gameservers available for this dimension";
                break;
            case 6:
                $err = "Character not available";
                break;
            case 7:
                $err = "Broken character";
                break;
            case 8:
                $err = "Playfield shutting down";
                break;

            case 9:
                $err = "Playfield full";
                break;
            case 10:
                $err = "Dimension full";
                break;
            case 11:
                $err = "Unable to log in";
                break;
            case 12:
                $err = "System Error";
                break;
            case 13:
                $err = "Account banned";
                break;
            case 14:
                $err = "Authentication failed";
                break;
            case 19:
                $err = "Another character is already logged in";
                break;
            case 23:
                $err = "Account frozen";
                break;
            case 25:
                $err = "Wrong universe version";
                break;
            case 30:
                $err = "Login timed out";
                break;
            case 33:
                $err = "Access denied to this dimension";
                break;
        }

        return $err;
    }
}

?>
