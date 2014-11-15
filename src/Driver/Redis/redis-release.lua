local key   = KEYS[1]
local token = ARGV[1]

if token ~= redis.call('GET', key) then
    return false
end

redis.call('DEL', key)

return true
