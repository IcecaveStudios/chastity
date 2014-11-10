-- KEYS[1] - the key that is used for the lock
-- ARGV[1] - the lock token

-- If the lock key does not contain the given token then bail early ...
if ARGV[1] ~= redis.call('GET', KEYS[1])
then
    return false
end

-- Delete the lock key ...
redis.call('DEL', KEYS[1])

return true
