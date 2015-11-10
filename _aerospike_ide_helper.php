<?php
class Aerospike
{
	// The key policy can be determined by setting OPT_POLICY_KEY to one of
	const POLICY_KEY_DIGEST = 268435456; // hashes (ns,set,key) data into a unique record ID (default)
	const POLICY_KEY_SEND = 268435457;   // also send, store, and get the actual (ns,set,key) with each record

	// The generation policy can be set using OPT_POLICY_GEN to one of
	const POLICY_GEN_IGNORE = 4294967296; // Write a record, regardless of generation
	const POLICY_GEN_EQ = 4294967297;     // Write a record, ONLY if given value is equal to the current record generation
	const POLICY_GEN_GT = 4294967298;     // Write a record, ONLY if given value is greater-than the current record generation

	// The retry policy can be determined by setting OPT_POLICY_RETRY to one of
	const POLICY_RETRY_NONE = 16; // do not retry an operation (default)
	const POLICY_RETRY_ONCE = 17; // allow for a single retry on an operation

	// By default writes will try to create or replace records and bins
	// behaving similar to an array in PHP. Setting
	// OPT_POLICY_EXISTS with one of these values will overwrite this.
	// POLICY_EXISTS_IGNORE (aka CREATE_OR_UPDATE) is the default value
	const POLICY_EXISTS_IGNORE = 256;            // interleave bins of a record if it exists
	const POLICY_EXISTS_CREATE = 257;            // create a record ONLY if it DOES NOT exist
	const POLICY_EXISTS_UPDATE = 258;            // update a record ONLY if it exists
	const POLICY_EXISTS_REPLACE = 259;           // replace a record ONLY if it exists
	const POLICY_EXISTS_CREATE_OR_REPLACE = 260; // overwrite the bins if record exists

	// Determines a handler for writing values of unsupported type into bins
	// Set OPT_SERIALIZER to one of the following:
	const SERIALIZER_NONE = 4096;
	const SERIALIZER_PHP = 4097; // default handler
	const SERIALIZER_JSON = 4098;
	const SERIALIZER_USER = 4099;

	// OPT_SCAN_PRIORITY can be set to one of the following:
	const SCAN_PRIORITY_AUTO = 1048576;   //The cluster will auto adjust the scan priority
	const SCAN_PRIORITY_LOW = null;    //Low priority scan.
	const SCAN_PRIORITY_MEDIUM = 1048578; //Medium priority scan.
	const SCAN_PRIORITY_HIGH = 1048579;   //High priority scan.

	// Options can be assigned values that modify default behavior
	const OPT_CONNECT_TIMEOUT = 1; // value in milliseconds, default: 1000
	const OPT_READ_TIMEOUT = 2;    // value in milliseconds, default: 1000
	const OPT_WRITE_TIMEOUT = 3; // value in milliseconds, default: 1000
	const OPT_POLICY_RETRY = 4; // set to a Aerospike::POLICY_RETRY_* value
	const OPT_POLICY_EXISTS = 5; // set to a Aerospike::POLICY_EXISTS_* value
	const OPT_SERIALIZER = 6; // set the unsupported type handler
	const OPT_SCAN_PRIORITY = 7; // set to a Aerospike::SCAN_PRIORITY_* value
	const OPT_SCAN_PERCENTAGE = 8; // integer value 1-100, default: 100
	const OPT_SCAN_CONCURRENTLY = 9; // boolean value, default: false
	const OPT_SCAN_NOBINS = 10; // boolean value, default: false
	const OPT_POLICY_KEY = 11; // records store the digest unique ID, optionally also its (ns,set,key) inputs
	const OPT_POLICY_GEN = 12; // set to array( Aerospike::POLICY_GEN_* [, $gen_value ] )

	// Aerospike Status Codes:
	//
	// Each Aerospike API method invocation returns a status code
	//  depending upon the success or failure condition of the call.
	//
	// The error status codes map to the C client
	//  src/include/aerospike/as_status.h

	//
	// Client status codes:
	//
	const OK = 0; // Success status
	const ERR = -1; // Generic error
	const ERR_CLIENT = -1; // Generic client error
	const ERR_PARAM = -2; // Invalid client parameter
	const ERR_CLUSTER = 11; // Cluster discovery and connection error
	const ERR_TIMEOUT = 9; // Client-side timeout error
	const ERR_THROTTLED = null; // Client-side request throttling (Deprecated, use ERR_CLIENT)

	//
	// Server status codes:
	//
	const ERR_SERVER = 1; // Generic server error
	const ERR_REQUEST_INVALID = 4; // Invalid request protocol or protocol field
	const ERR_SERVER_FULL = 8; // Node running out of memory/storage
	const ERR_CLUSTER_CHANGE = 7; // Cluster state changed during the request
	const ERR_UNSUPPORTED_FEATURE = 16;
	const ERR_DEVICE_OVERLOAD = 18; // Node storage lagging write load
	// Record specific
	const ERR_RECORD = null; // Generic record error (Deprecated)
	const ERR_RECORD_BUSY = 14; // Hot key: too many concurrent requests for the record
	const ERR_RECORD_NOT_FOUND = 2;
	const ERR_RECORD_EXISTS = 5;
	const ERR_RECORD_GENERATION = 3; // Write policy regarding generation violated
	const ERR_RECORD_TOO_BIG = 13; // Record written cannot fit in storage write block
	const ERR_BIN_TYPE = null; // Bin modification failed due to value type
	const ERR_RECORD_KEY_MISMATCH = 19;
	// Scan operations:
	const ERR_SCAN = null; // Generic scan error (Deprecated, use ERR)
	const ERR_SCAN_ABORTED = 15; // Scan aborted by the user
	// Query operations:
	const ERR_QUERY = 213; // Generic query error
	const ERR_QUERY_ABORTED = 210; // Query aborted by the user
	const ERR_QUERY_QUEUE_FULL = 211;
	// Index operations:
	const ERR_INDEX = 204; // Generic secondary index error
	const ERR_INDEX_OOM = 202; // Index out of memory
	const ERR_INDEX_NOT_FOUND = 201;
	const ERR_INDEX_FOUND = 200;
	const ERR_INDEX_NOT_READABLE = 203;
	const ERR_INDEX_NAME_MAXLEN = 205;
	const ERR_INDEX_MAXCOUNT = 206; // Max number of indexes reached
	// UDF operations:
	const ERR_UDF = 100; // Generic UDF error
	const ERR_UDF_NOT_FOUND = 1301; // UDF does not exist
	const ERR_UDF_FILE_NOT_FOUND = null; // Source file for the module not found
	const ERR_LUA_FILE_NOT_FOUND = 1302; // Source file for the module not found

	// Status values returned by scanInfo()
	const SCAN_STATUS_UNDEF = 16777216;      // Scan status is undefined.
	const SCAN_STATUS_INPROGRESS = 16777217; // Scan is currently running.
	const SCAN_STATUS_ABORTED = 16777218;    // Scan was aborted due to failure or the user.
	const SCAN_STATUS_COMPLETED = 16777219;  // Scan completed successfully.

	// Logger
	const LOG_LEVEL_OFF = 6;
	const LOG_LEVEL_ERROR = 5;
	const LOG_LEVEL_WARN = 4;
	const LOG_LEVEL_INFO = 3;
	const LOG_LEVEL_DEBUG = 2;
	const LOG_LEVEL_TRACE = 1;

	// Query Predicate Operators
	const OP_EQ = '=';
	const OP_BETWEEN = 'BETWEEN';

	// Multi-operation operators map to the C client
	//  src/include/aerospike/as_operations.h
	const OPERATOR_WRITE = 0;
	const OPERATOR_READ = 1;
	const OPERATOR_INCR = 2;
	const OPERATOR_PREPEND = 4;
	const OPERATOR_APPEND = 5;
	const OPERATOR_TOUCH = 8;

	// UDF types
	const UDF_TYPE_LUA = 65536;

	// bin types
	const INDEX_TYPE_STRING = 1;
	const INDEX_TYPE_INTEGER = 2;


	// lifecycle and connection methods
	/**
	 * @param array $config
	 * @param bool $persistent_connection
	 * @param array $options
	 * @return int
	 */
	public function __construct(array $config, $persistent_connection = true, array $options = []) {}

	/**
	 * @return void
	 */
	public function __destruct() {}

	/**
	 * @return boolean
	 */
	public function isConnected() {}

	/**
	 * @return void
	 */
	public function close() {}

	/**
	 * @return void
	 */
	public function reconnect() {}

	// error handling methods
	/**
	 * @return string
	 */
	public function error() {}

	/**
	 * @return int
	 */
	public function errorno() {}

	/**
	 * @param int $log_level
	 * @return void
	 */
	public function setLogLevel($log_level) {}

	/**
	 * @param callable $log_handler
	 * @return void
	 */
	public function setLogHandler(callable $log_handler) {}

	// key-value methods
	/**
	 * @param string $ns
	 * @param string $set
	 * @param int|string $pk
	 * @return array
	 */
	public function initKey($ns, $set, $pk) {}

	/**
	 * @param array $key
	 * @param array $bins
	 * @param int $ttl
	 * @param array $options
	 * @return int
	 */
	public function put(array $key, array $bins, $ttl = 0, array $options = []) {}

	/**
	 * @param array $key
	 * @param array $record
	 * @param array $filter
	 * @param array $options
	 * @return int
	 */
	public function get(array $key, array &$record, array $filter = [], array $options = []) {}

	/**
	 * @param array $key
	 * @param array $metadata
	 * @param array $options
	 * @return int
	 */
	public function exists(array $key, array &$metadata, array $options = []) {}

	/**
	 * @param array $key
	 * @param int $ttl
	 * @param array $options
	 * @return int
	 */
	public function touch(array $key, $ttl = 0, array $options = []) {}

	/**
	 * @return int
	 */
	public function remove(array $key, array $options = []) {}

	/**
	 * @return int
	 */
	public function removeBin(array $key, array $bins, array $options = []) {}

	/**
	 * @param array $key
	 * @param string $bin
	 * @param int $offset
	 * @param int $initial_value
	 * @param array $options
	 * @return int
	 */
	public function increment(array $key, $bin, $offset, $initial_value = 0, array $options = []) {}

	/**
	 * @param array $key
	 * @param string $bin
	 * @param string $value
	 * @param array $options
	 * @return int
	 */
	public function append(array $key, $bin, $value, array $options = []) {}

	/**
	 * @param array $key
	 * @param string $bin
	 * @param string $value
	 * @param array $options
	 * @return int
	 */
	public function prepend(
		array $key,
		$bin,
		$value,
		array $options = []
	) {}

	/**
	 * @param array $key
	 * @param array $operations
	 * @param array $returned
	 * @return int
	 */
	public function operate(array $key, array $operations, array &$returned = []) {}

	// unsupported type handler methods
	/**
	 * @param callable $serialize_cb
	 * @return null
	 */
	public static function setSerializer($serialize_cb) {}

	/**
	 * @param callable $unserialize_cb
	 * @return null
	 */
	public static function  setDeserializer($unserialize_cb) {}

	// batch operation methods
	/**
	 * @param array $keys
	 * @param array $records
	 * @param array $filter
	 * @param array $options
	 * @return int
	 */
	public function getMany(array $keys, array &$records, array $filter = [], array $options = []) {}

	/**
	 * @param array $keys
	 * @param array $metadata
	 * @param array $options
	 */
	public function existsMany(array $keys, array &$metadata, array $options = []) {}

	// UDF methods
	/**
	 * @param string $path
	 * @param string $module
	 * @param int $language
	 * @return int
	 */
	public function register($path, $module, $language = Aerospike::UDF_TYPE_LUA) {}

	/**
	 * @param string $module
	 * @return int
	 */
	public function deregister($module) {}

	/**
	 * @param array $modules
	 * @param int $language
	 * @return int
	 */
	public function listRegistered(array &$modules, $language = 0) {}

	/**
	 * @param string $module
	 * @param string $code
	 * @return int
	 */
	public
	function getRegistered(
		$module,
		&$code
	) {}

	/**
	 * @param array $key
	 * @param $module
	 * @param $function
	 * @param array $args
	 * @param mixed $returned
	 * @param array $options
	 * @return int
	 */
	public function apply(array $key, $module, $function, array $args = [], &$returned = null, array $options = []) {}

	/**
	 * @param string $ns
	 * @param string $set
	 * @param array $where
	 * @param string $module
	 * @param string $function
	 * @param array $args
	 * @param mixed $returned
	 * @param array $options
	 * @return int
	 */
	public function aggregate($ns, $set, array $where, $module, $function, array $args, &$returned, array $options = []) {}

	/**
	 * @param string $ns
	 * @param string $set
	 * @param string $module
	 * @param string $function
	 * @param array $args
	 * @param int $scan_id
	 * @return int
	 */
	public function scanApply($ns, $set, $module, $function, array $args, &$scan_id, array $options = []) {}

	/**
	 * @param int $scan_id
	 * @param array $info
	 * @param array $options
	 * @return int
	 */
	public function scanInfo($scan_id, array &$info, array $options = []) {}

	// query and scan methods
	/**
	 * @param string $ns
	 * @param string $set
	 * @param array $where
	 * @param callable $record_cb
	 * @param array $select
	 * @param array $options
	 * @return int
	 */
	public function query($ns, $set, array $where, $record_cb, array $select = [], array $options = []) {}

	/**
	 * @param string $ns
	 * @param string $set
	 * @param callable $record_cb
	 * @param array $select
	 * @param array $options
	 * @return int
	 */
	public function scan($ns, $set, $record_cb, array $select = [], array $options = []) {}

	/**
	 * @param string $bin
	 * @param int|string $val
	 * @return array
	 */
	public function predicateEquals($bin, $val) {}

	/**
	 * @param string $bin
	 * @param int $min
	 * @param int $max
	 * @return array
	 */
	public function predicateBetween($bin, $min, $max) {}

	// admin methods
	/**
	 * @param string $ns
	 * @param string $set
	 * @param string $bin
	 * @param int $type
	 * @param string $name
	 * @param array $options
	 * @return int
	 */
	public function createIndex($ns, $set, $bin, $type, $name, array $options = []) {}

	/**
	 * @param string $ns
	 * @param string $name
	 * @param array $options
	 * @return int
	 */
	public function dropIndex($ns, $name, array $options = []) {}

	// info methods
	/**
	 * @param string $request
	 * @param string $response
	 * @param array $host
	 * @param array $options
	 * @return int
	 */
	public function info($request, &$response, array $host = [], array $options = []) {}

	/**
	 * @param string $request
	 * @param array $config
	 * @param array $options
	 * @return array
	 */
	public function infoMany($request, array $config = [], array $options = []) {}

	/**
	 * @return array
	 */
	public function getNodes() {}
}
