<?php
/*
 * This file is a part of "furqansiddiqui/ethereum-php" package.
 * https://github.com/furqansiddiqui/ethereum-php
 *
 * Copyright (c) Furqan A. Siddiqui <hello@furqansiddiqui.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code or visit following link:
 * https://github.com/furqansiddiqui/ethereum-php/blob/master/LICENSE
 */

declare(strict_types=1);

use FurqanSiddiqui\Ethereum\Ethereum;
use Comely\DataTypes\DataTypes;
use FurqanSiddiqui\Ethereum\Exception\RPCInvalidResponseException;
use FurqanSiddiqui\Ethereum\Math\Integers;
use FurqanSiddiqui\Ethereum\RPC\Models\Block;
use FurqanSiddiqui\Ethereum\RPC\Models\Transaction;
use FurqanSiddiqui\Ethereum\RPC\Models\TransactionReceipt;

class CustomRPC extends FurqanSiddiqui\Ethereum\RPC\AbstractRPCClient
{
    /** @var string */
    private string $hostname;
    /** @var int|null */
    private ?int $port;

    /**
     * GethRPC constructor.
     * @param Ethereum $eth
     * @param string $host
     * @param int|null $port
     */
    public function __construct(Ethereum $eth, string $host, ?int $port = null)
    {
        parent::__construct($eth);

        $this->hostname = $host;
        $this->port = $port && $port <= 0xffff ? $port : null;
    }

    /**
     * @return string
     */
    protected function getServerURL(): string
    {
        $url = $this->hostname;
        if (!preg_match('/^(http|https):\/\//i', $url)) {
            $url = "http://" . $url;
        }

        if ($this->port) {
            $url .= ":" . $this->port;
        }

        return $url;
    }

    public function personal_newAccount(string $password) : string
    {
        return $this->call("personal_newAccount", [$password]);
    }

    public function eth_gasPrice()
    {
        $gas_price = $this->call("eth_gasPrice", []);
        if (!DataTypes::isBase16($gas_price)) {
            throw RPCInvalidResponseException::InvalidDataType("eth_gasPrice", "Base16", gettype($gas_price));
        }

        $gas_price = $this->eth->wei()->fromWei(Integers::Unpack($gas_price))->eth();
        if (!DataTypes::isNumeric($gas_price)) {
            throw RPCInvalidResponseException::InvalidDataType("eth_getBalance", "Base10/Decimal", "Invalid");
        }
        var_dump($gas_price);
    }

    /**
     * @param string $accountId
     * @param string $scope
     * @return string
     * @throws \FurqanSiddiqui\Ethereum\Exception\RPCException
     */
    public function eth_getBalance(string $accountId, string $scope = "latest"): string
    {
        if(is_numeric($scope)) {
            $scope = '0x' . dechex($scope);
        } else if (!in_array($scope, ["latest", "earliest", "pending"])) {
            throw new \InvalidArgumentException('Invalid block scope; Valid values are "latest", "earliest" and "pending"');
        }


        $balance = $this->call("eth_getBalance", [$accountId, $scope]);
        if (!DataTypes::isBase16($balance)) {
            throw RPCInvalidResponseException::InvalidDataType("eth_getBalance", "Base16", gettype($balance));
        }

        $balance = $this->eth->wei()->fromWei(Integers::Unpack($balance))->eth();
        if (!DataTypes::isNumeric($balance)) {
            throw RPCInvalidResponseException::InvalidDataType("eth_getBalance", "Base10/Decimal", "Invalid");
        }

        return $balance;
    }
}
