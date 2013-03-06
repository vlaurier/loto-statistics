<?php

/**
 * This class allows to perform some operations on games datas.
 * @author vlaurier
 */
class ResultsManager {

    /**
     * Store games datas
     * 
     * @var array   
     */
    protected $_aDatas = array();
    
    /**
     * List all available games
     * 
     * @var array 
     */
    protected $_aGames = array('nouveau_loto', 'loto');

    /**
     * Constructor
     * 
     * @param[optional] string|array $aGames
     * @param[optional] boolean $bRefresh
     */
    public function __construct($aGames = NULL, $bRefresh = FALSE) {

        // refresh results if asked
        if ($bRefresh) {
            self::refresh();
        }
        $this->load($aGames);
    }

    /**
     * Populate aDatas with asked games.
     * By default, all games are retrieved.
     * 
     * @param[optional] NULL|array|string $aGames
     * @throws Exception
     */
    public function load($aGames = NULL) {

        if (NULL === $aGames) {
            $aGames = $this->_aGames;
        }
        if (is_array($aGames)) {
            foreach ($aGames as $sGame) {
                $this->_aDatas[$sGame] = self::arrayFromSerializedFile($sGame);
            }
        } elseif (is_string($aGames)) {
            $this->_aDatas[$aGames] = self::arrayFromSerializedFile($sGames);
        } else {
            throw new Exception('Parameter must be an array, a string or null');
        }
    }

    /**
     * 
     * @param string $sFile 
     * @return type
     */
    public static function arrayFromSerializedFile($sFile){
        $aFile = file('results/php/' .$sFile. '.php');
        return unserialize(urldecode($aFile[0]));
    }
    
    
    /**
     * Return the results for the wanted game
     * 
     * @param string $sGame
     * @return array
     * @throws Exception
     */
    public function __get($sGame) {

        if (!in_array($sGame, $this->_aGames)) {
            throw new Exception('Cannot call this property');
        }
        if (!array_key_exists($sGame, $this->_aDatas)) {
            $this->load($sGame);
        }
        return $this->_aDatas[$sGame];
    }

    /**
     * Refresh results by downloading zip and extract csv
     * 
     * @return void
     */
    static public function refresh() {

        // TODO: replace hard coding to load games from config
        $sGame = 'nouveau_loto';
        $file = 'https://media.fdj.fr/generated/game/loto/' . $sGame . '.zip';
        $newfile = 'results/zip/' . $sGame . '.zip';

        if (!copy($file, $newfile)) {
            echo "La copie $file du fichier a échoué...\n";
        }
        $zip = new ZipArchive;
        $res = $zip->open('results/zip/' . $sGame . '.zip');
        if ($res === TRUE) {
            $zip->extractTo('results/csv');
            $zip->close();
        } else {
            echo 'échec, code:' . $res;
        }
    }

    /**
     * Process datas from csv file
     * @return array $aResults
     * @throws Exception
     */
    static public function computeToPhp() {
        // Try to open the csv file
        if (($handle = fopen("results/csv/nouveau_loto.csv", "r")) === FALSE) {
            throw new Exception('Could not open file');
        }

        // Fetch informations from file
        $aCsvLines = array();
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $aCsvLines[] = $data;
        }
        // ... and close connection.
        fclose($handle);

        // Retrieve fields in array
        $aFields = array_shift($aCsvLines);
        $aFields = explode(';', $aFields[0]);

        // Retrieve all results
        $aResults = array();

        foreach ($aCsvLines as $result) {
            $stringValue = '';
            foreach ($result as $line) {
                $stringValue.=$line;
            }
            
            $temp = array_combine($aFields, explode(';', $stringValue));
            array_pop($temp);
            $aResults[] = $temp;           
        }
        
        // Serialize the results to save them
        $sSerialized = urlencode(serialize($aResults));
        $fp = fopen('results/php/nouveau_loto.php', 'w');
        fwrite($fp, $sSerialized);
        fclose($fp);

        return self::arrayFromSerializedFile('nouveau_loto');
    }

}
