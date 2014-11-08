if redis.call('GET', KEYS[1]) != ARGV[1]
then
    return false
end

redis.call(
    'EXPIRE',
    KEYS[1],
    redis.call('PTTL') + ARGV[1]
)

return true
