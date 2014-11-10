-- KEYS[1] - the key that is used for the lock
-- ARGV[1] - the lock token
-- ARGV[2] - the amount to extend the TTL by, in millis

-- If the lock key does not contain the given token then bail early ...
if ARGV[1] ~= redis.call('GET', KEYS[1])
then
    return false
end

-- Extend the TTL ...
redis.call(
    'EXPIRE',
    KEYS[1],
    redis.call('PTTL') + ARGV[2]
)

return true
