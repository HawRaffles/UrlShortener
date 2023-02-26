<?php

namespace Tars\UrlShortener\Actions;

use InvalidArgumentException;
use Tars\UrlShortener\Interfaces\ICheckUrl;
use Tars\UrlShortener\Interfaces\IUrlDecoder;
use Tars\UrlShortener\Interfaces\IUrlEncoder;

class ConvertUrl implements IUrlEncoder, IUrlDecoder
{
    private string $dataFile;
    private array $encodeData = [];
    private ICheckUrl $urlValidator;
    private string $validDate;

    public function __construct(string $dataFile, $urlValidator, string $validDate)
    {
        $this->dataFile = $dataFile;
        $this->urlValidator = $urlValidator;
        $this->validDate = $validDate;
        if (file_exists($this->dataFile))
            $this->encodeData = json_decode(file_get_contents($this->dataFile), true);
    }

    public function interactiveEncode(): string
    {
        $url = $this->urlValidator->GetInput();
        return $this->encode($url);
    }

    public function encode($url): string
    {
        if (!$this->urlValidator->CheckUrl($url))
            throw new InvalidArgumentException('Невалідний URL: ' . $url);
        $hash = md5($url);
        $key = substr($hash, 0, 6);
        $this->SaveData($url, $key);
        return $key;
    }

    public function decode(string $code): string
    {
        if (!isset($this->encodeData[$code]))
            throw new InvalidArgumentException('Вказаний код ' . $code . ' URL-у відсутній в базі!');
        return $this->encodeData[$code]['url'];
    }

    private function SaveData(string $url, string $key)
    {
        if (!in_array($url, $this->encodeData))
            $this->encodeData[$key] = ['url' => $url, 'until' => $this->validDate];

        if (!file_exists(realpath($this->dataFile))) {
            $pathData = pathinfo($this->dataFile);
            mkdir($pathData['dirname']);
        }

        $fileData = fopen($this->dataFile, "w+");
        flock($fileData, LOCK_EX);
        fwrite($fileData, json_encode($this->encodeData));
        fclose($fileData);
    }
}
