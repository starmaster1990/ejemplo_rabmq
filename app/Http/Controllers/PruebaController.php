<?php
namespace App\Http\Controllers;

use Bschmitt\Amqp\Facades\Amqp;

class PruebaController extends Controller{
    
    /**
     * Publica en la cola hello
     */
    public function queue(){
        try{
            $data = [ 'id' => self::guidv4(), 'text' =>'lumen prueba'];
            Amqp::publish('routing-key', json_encode($data) , ['queue' => 'hello']);            
            return response()->json($data);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],500);
        }
    }   
    
    /**
     * Publicacion en cola mediante exchange fanout
     */
    public function fanout(){
        try{
            for ($i=0; $i < 5; $i++) { 
                $data[$i] = [ 'id' => self::guidv4(), 'text' =>'lumen fanout prueba'];
                Amqp::publish('', json_encode($data[$i]) , [
                    'exchange_type' => 'fanout',
                    'exchange' => 'my-fanout',
                ]);
            }
            return response()->json($data);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],500);
        }
    } 
    
    /**
     * Publicacion en cola mediante exchange direct
     */
    public function direct(){
        try{
            for ($i=0; $i < 5; $i++) { 
                $data[$i] = [ 'id' => self::guidv4(), 'text' =>'lumen direct prueba'];
                Amqp::publish('abc', json_encode($data[$i]) , [
                    'exchange_type' => 'direct',
                    'exchange' => 'my-direct',
                ]);
            }
            return response()->json($data);
        }catch(\Exception $e){
            return response()->json(['message' => $e->getMessage()],500);
        }
    } 
    
    private static function guidv4($data = null) {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);
    
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    
        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
