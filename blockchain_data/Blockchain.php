<?php
session_start();

class Block {
    public $index;
    public $timestamp;
    public $data;
    public $previous_hash;
    public $hash;

    public function __construct($index, $timestamp, $data, $previous_hash = '') {
        $this->index = $index;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->previous_hash = $previous_hash;
        $this->hash = $this->calculateHash();
    }

    public function calculateHash() {
        return hash('sha256', $this->index . $this->timestamp . json_encode($this->data) . $this->previous_hash);
    }
}

class Blockchain {
    public $chain;

    public function __construct() {
        $this->chain = [$this->createGenesisBlock()];
    }

    private function createGenesisBlock() {
        return new Block(0, date('Y-m-d H:i:s'), "Genesis Block", "0");
    }

    public function getLastBlock() {
        return $this->chain[count($this->chain) - 1];
    }

    public function addBlock($newBlock) {
        $newBlock->previous_hash = $this->getLastBlock()->hash;
        $newBlock->hash = $newBlock->calculateHash();
        $this->chain[] = $newBlock;
    }

    public function isChainValid() {
        for ($i = 1; $i < count($this->chain); $i++) {
            $current = $this->chain[$i];
            $previous = $this->chain[$i - 1];

            if ($current->hash !== $current->calculateHash()) {
                return false;
            }

            if ($current->previous_hash !== $previous->hash) {
                return false;
            }
        }
        return true;
    }
}

// Helper: Load or Save Blockchain to JSON File
function getBlockchainFile($product_id) {
    return __DIR__ . "/blockchain_data/product_$product_id.json";
}

function loadBlockchain($product_id) {
    $file = getBlockchainFile($product_id);
    if (!file_exists($file)) {
        return new Blockchain();
    }

    $json = json_decode(file_get_contents($file), true);
    $blockchain = new Blockchain();
    $blockchain->chain = [];

    foreach ($json as $blockData) {
        $block = new Block(
            $blockData['index'],
            $blockData['timestamp'],
            $blockData['data'],
            $blockData['previous_hash']
        );
        $block->hash = $blockData['hash'];
        $blockchain->chain[] = $block;
    }

    return $blockchain;
}

function saveBlockchain($product_id, $blockchain) {
    $file = getBlockchainFile($product_id);
    $data = [];

    foreach ($blockchain->chain as $block) {
        $data[] = [
            'index' => $block->index,
            'timestamp' => $block->timestamp,
            'data' => $block->data,
            'previous_hash' => $block->previous_hash,
            'hash' => $block->hash,
        ];
    }

    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

function addProductTransaction($product_id, $data) {
    $blockchain = loadBlockchain($product_id);
    $index = count($blockchain->chain);
    $block = new Block($index, date('Y-m-d H:i:s'), $data);
    $blockchain->addBlock($block);
    saveBlockchain($product_id, $blockchain);
}

function getProductBlockchainHistory($product_id) {
    $blockchain = loadBlockchain($product_id);
    $history = [];

    foreach ($blockchain->chain as $block) {
        $history[] = [
            'index' => $block->index,
            'timestamp' => $block->timestamp,
            'data' => $block->data,
            'previous_hash' => $block->previous_hash,
            'hash' => $block->hash,
        ];
    }

    return $history;
}
