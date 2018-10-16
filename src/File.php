<?php

namespace Recca0120\VideoFinder;

class File
{
    private $numberPrefix = [
        'ABS',
        'ABP',
        'BLO',
        'TDT',
        'JCN',
        'DEL',
        'JOB',
        'JBS',
        'EVO',
        'YRZ',
        'INU',
        'AKA',
        'BGN',
        'MAS',
        'CHN',
        'DIC',
        'ESK',
        'FTN',
        'JAN',
        'SGA',
        'SIRO',
        '200GANA',
        '259LUXU',
        'SOE',
        'SNIS',
        'MIDD',
        'MIDE',
        'MIAD',
        'MIGD',
        'MIMK',
        'IPSD',
        'IPTD',
        'IPZ',
        'SUPD',
        'PGD',
        'ADN',
        'RBD',
        'SHKD',
        'TEK',
        'EBOD',
        'EYAN',
        'EYAN',
        'PPPD',
        'KAWD',
        'KWBD',
        'HND',
        'JUFD',
        'TYOD',
        'TPPN',
        'BF',
        'ZUKO',
        'DASD',
        'BLK',
        'BID',
        'BBI',
        'CJOD',
        'CLUB',
        'WANZ',
        'MMND',
        'TEAM',
        'HHK',
        'ALB',
        'MUKD',
        'MUDR',
        'MUM',
        'ANND',
        'BBAN',
        'MOND',
        'SPRD',
        'JUC',
        'JUX',
        'OBA',
        'URE',
        'MDYD',
        'MEYD',
        'VENU',
        'VEMA',
        'VAGU',
        'DV',
        'DVAJ',
        'SRXV',
        'XV',
        'XVSE',
        'HRDV',
        'HODV',
        'ADZ',
        'MILD',
        'MKMP',
        'MDS',
        'MDTM',
        'SABA',
        'SAMA',
        'BOKD',
        'STAR',
        'SACE',
        'SDNM',
        'SDAB',
        'SDSI',
        'SDDE',
        'SDMU',
        'SSNI',
        'FSET',
        'LXVS',
        'NAKA',
        'ONSD',
        'SCOP',
    ];

    private $path;

    private $name;

    private $extension;

    private $number;

    public function __construct()
    {
        usort($this->numberPrefix, function ($a, $b) {
            return strlen($a) < strlen($b);
        });
    }

    public function __toString()
    {
        return $this->name();
    }

    public function getPath()
    {
        return $this->path;
    }

    public function setPath($path)
    {
        $this->path = $path;
        $this->name = strtoupper(pathinfo($path, PATHINFO_FILENAME));
        $this->extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $this->number = $this->parse($this->name);

        return $this;
    }

    public function name()
    {
        return $this->number.'.'.$this->extension;
    }

    public function number()
    {
        return $this->number;
    }

    private function parse($name)
    {
        $name = str_replace('@SIS001@', '', $name);

        foreach ($this->numberPrefix as $keyword) {
            if (($pos = stripos($name, $keyword)) !== false) {
                try {
                    return $this->guessNumer(substr($name, $pos));
                } catch (NotFoundException $e) {
                }
            }
        }

        return $this->guessNumer($name);
    }

    private function guessNumer($name)
    {
        if ((bool) preg_match('/[A-Za-z]{2,5}[\\-_\s]?\\d{2,5}/', $name, $m) !== false) {
            $number = str_replace(' ', '', $m[0]);

            return strtoupper(
                strpos($number, '-') === false
                    ? preg_replace('/(\W+)|(\d+)/', '$1-$2', $number)
                    : $number
            );
        }

        if ((bool) preg_match('/\d{6}\\-\d{3}/i', $name, $m) !== false) {
            return strtoupper($m[0]);
        }

        throw new NotFoundException(sprintf('[%s] can\'t not parse number', $name));
    }
}
