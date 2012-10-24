<?php

class Team {
	
	protected $m_aScoreSequence;
	protected $m_nScore;
	
	public function __construct() {
		$this->m_aScoreSequence = array( 0, 15, 30, 40, 50 );
		$this->m_nScore = 0;
	}
	
	/**
	 * The responsibility should be moved to a "ScoreBoard"-type class
	 * 
	 * @param Team $inoOpponent
	 * @return boolean
	 */
	public function pointScored( Team $inoOpponent ) {
		if ( $inoOpponent->hasAdvantage() ) {
			$inoOpponent->reduceScore();
			return false;
		}
		if ( $this->hasAdvantage() ) {
			return true;
		}
		
		$this->m_nScore++;
		return ( $this->getRealScore() > 40 && $inoOpponent->getRealScore() < 40 );
	}
	
	public function reduceScore() {
		$this->m_nScore--;
	}

	public function getScore() {
		$nRealScore = $this->getRealScore();
		return ( $nRealScore == 50? 'ADV': $nRealScore );
	}
	
	public function getRealScore() {
		return $this->m_aScoreSequence[$this->m_nScore];
	}
	
	public function hasAdvantage() {
		return ( $this->getRealScore() == 50 );
	}
}

class Tennis_Match {
	
	protected $aTeams;
	public $m_mWinner;
	
	public function __construct( $insServingTeam ) {
		$this->aTeams = array(
			'a'	=> new Team(),
			'b'	=> new Team()
		);
		$this->m_mWinner = false;
		$this->m_sServer = $insServingTeam;
		
		printf( '<h3>Team %s is serving</h3>', $this->m_sServer );
	}
	
	public function __clone() {
		$this->aTeams = array(
			'a'	=> clone $this->aTeams['a'],
			'b'	=> clone $this->aTeams['b']
		);
	}
	
	/**
	 * @param string $insTeam
	 */
	public function pointScored( $insTeam, $infEchoScore = true ) {
		if ( $this->gameHasWinner() ) {
			echo "There is a winner: ".$this->m_mWinner;
			return;
		}
		$nScoreTeamA = $this->aTeams['a']->getScore();
		$nScoreTeamB = $this->aTeams['b']->getScore();
		
		if ( $this->aTeams[$insTeam]->pointScored( $this->aTeams[$insTeams =='a'?'b':'a'] ) ) {
			$this->m_mWinner = $insTeam;
		}
		if ( $infEchoScore ) {
			echo $this->getGameScore().'<br />';
		}
	}
	
	public function gameHasWinner() {
		return ( $this->m_mWinner !== false );
	}
	
	public function gameHasAdvantage() {
		return ( $this->aTeams['a']->hasAdvantage() || $this->aTeams['b']->hasAdvantage() );
	}
	
	public function gameHasDeuce() {
		return ( $this->aTeams['a']->getRealScore() == 40 && $this->aTeams['b']->getRealScore() == 40 );
	}
	
	public function someoneHas40() {
		return ( $this->aTeams['a']->getRealScore() == 40 || $this->aTeams['b']->getRealScore() == 40 );
	}
	
	/**
	 * Uses PHP's clone feature and the magic __clone
	 */
	public function isBreakPoint() {
		if ( !$this->someoneHas40() ) {
			return false;
		}
		
		$oTest = clone $this;
		$oTest->pointScored( ($this->m_sServer == 'a'? 'b': 'a' ), false );
		return ( $oTest->m_mWinner == ($this->m_sServer == 'a'? 'b': 'a' ) );
	}
	
	public function getGameScore() {
		if ( $this->m_mWinner !== false ) {
			return $this->m_mWinner.' wins';
		}
		if ( $this->gameHasDeuce() ) {
			return 'DEUCE';
		}
		$sOutput = $this->aTeams['a']->getScore().' : '.$this->aTeams['b']->getScore();
		if ( $this->isBreakPoint() ) {
			$sOutput .= ' ---- BREAK POINT';
		}
		return $sOutput;
	}
}

$oMatch = new Tennis_Match( rand( 0, 1 ) == 0? 'a': 'b' );
while ( !$oMatch->gameHasWinner() ) {
	$sTeam = rand( 0, 1 ) == 0? 'a': 'b';
	$oMatch->pointScored( $sTeam );
}
