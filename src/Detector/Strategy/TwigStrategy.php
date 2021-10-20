<?php declare(strict_types=1);
/*
 * This file is part of PHPWEB Copy/Paste Detector (PHPWEBCPD).
 *
 * (c) Enrique Somolinos <enrique.somolinos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPWEBCPD\Detector\Strategy;

use FriendsOfTwig\Twigcs\Container;
use FriendsOfTwig\Twigcs\TwigPort\TwigLexer;
use Twig\Environment;
use Twig\Lexer;
use Twig\Loader\ArrayLoader;
use Twig\Source;

class TwigStrategy extends DefaultStrategy
{
    protected function getTokens(string $buffer): array
    {
        $twigEnvironment = new Environment(new ArrayLoader());
        $twigEnvironment->setLexer(new Lexer($twigEnvironment));
        $twigTokens = $twigEnvironment->tokenize(
            new Source($buffer, 'src', '')
        );
        $numTokens = 0;
        $tokens = [];
        while (!$twigTokens->isEOF()) {
            $currentTwigToken = $twigTokens->next();
            $token[0] = $currentTwigToken->getType();
            $token[2] = $currentTwigToken->getLine();
            $token[1] = trim(strval($currentTwigToken->getValue()));
            if ($currentTwigToken->getType() == 0) {
                $values = explode("\n", $currentTwigToken->getValue());
                $values2 = [];
                foreach ($values as $value) {
                    $value = preg_replace('/[^\S\r\n]+/', ' ', trim($value));
                    $values2[] = trim($value);
                }
                $token[1] = implode("\n", $values2);
            }
            $tokens[]  =$token;
            $numTokens++;
        }
        return $tokens;
    }

    protected function isVariableToken(array $token): bool
    {
        return isset($token[0]) ? $token[0] === TwigLexer::STATE_VAR : false;
    }
}
