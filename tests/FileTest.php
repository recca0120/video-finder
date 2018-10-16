<?php

namespace Recca0120\VideoFinder\Tests;

use PHPUnit\Framework\TestCase;
use Recca0120\VideoFinder\File;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class FileTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @test
     * @dataProvider numberProvider
     */
    public function test_set_name($name, $expected)
    {
        $file = (new File())->setPath($name);

        $this->assertSame($expected, $file->number());
        $this->assertSame($expected.'.'.pathinfo($name, PATHINFO_EXTENSION), (string) $file);
    }

    public function numberProvider()
    {
        return [
            ['9SNIS-027 - 宇都宮しをん、イキます。 (ブルーレイ).mp4', 'SNIS-027'],
            ['EBOD-320 - 着エロコスプレイヤーの恥ずかしい絶頂 山手栞.avi', 'EBOD-320'],
            ['TBL-016 - イキ過ぎた着エロ 02.avi', 'TBL-016'],
            ['YSN-418.mp4', 'YSN-418'],
            ['ABP329.mp4', 'ABP-329'],
            ['(Caribbean)(090913-426)いいなり爆乳現役某音大生 立川理恵.mp4', '090913-426'],
            ['071614-644-carib-high_1.mp4', '071614-644'],
            ['第一會所新片@SIS001@(S1)(SNIS-640)超高級Jcup風俗嬢_RION/(S1)(SNIS-640)超高級Jcup風俗嬢 RION.avi', 'SNIS-640'],
            ['第一會所新片@SIS001@(million)(MILD-853)緊縛令嬢_2_神咲詩織', 'MILD-853'],
            ['BP-575   120 44-openload -85 -.mp4', 'BP-575'],
            ['https1llr9xoloadcdnnetdllcc4uAfBEL7MkbIz2GTt30sSAMA-984mp4mimetrue', 'SAMA-984'],
            ['httpsoql95goloadcdnnetdllZnT1IbbFgOMv7-WMHrbXUUFSET-627mp4mimetrue', 'FSET-627'],
            ['HDSSNI-054    -openload -85 -.mp4', 'SSNI-054'],
            ['FHDabp-556   15-openload -85 -.mp4', 'ABP-556'],
            ['[S1] 【数量限定】常にハメシロ・イキ顔同時に丸見えアングルV 超卑猥ヌキ挿しアクションとエロイ表情を合わせて見てヌク新感覚映像 [TKSNIS-888]/葵 - [S1] 【数量限定】常にハメシロ・イキ顔同時に丸見えアングルV 超卑猥ヌキ挿しアクションとエロイ表情を合わせて見てヌク新感覚映像 [TKSNIS-888].mp4', 'SNIS-888'],
        ];
    }
}
