if redis.call('GET', KEYS[1]) != ARGV[1]
then
    return false
end

redis.call('DEL', KEYS[1])

return true
