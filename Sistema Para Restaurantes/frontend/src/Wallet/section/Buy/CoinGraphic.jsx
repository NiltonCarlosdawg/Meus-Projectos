import React, { useState, useEffect } from 'react';
import { ComposedChart, XAxis, YAxis, Tooltip, Legend, Bar } from 'recharts';

// Sample months mapping
const months = {
  "01": "Jan", "02": "Feb", "03": "Mar", "04": "Apr", "05": "May", "06": "Jun",
  "07": "Jul", "08": "Aug", "09": "Sep", "10": "Oct", "11": "Nov", "12": "Dec"
};

// Custom Candlestick component
const CustomCandlestick = ({ x, y, width, high, low, open, close }) => {
  const isUp = close >= open;
  const color = isUp ? '#2ecc71' : '#e74c3c';
  const bodyHeight = Math.abs(open - close);
  const bodyY = isUp ? y - bodyHeight : y;

  return (
    <g>
      {/* Wick */}
      <line
        x1={x + width / 2}
        x2={x + width / 2}
        y1={y - (high - Math.max(open, close))}
        y2={y + (Math.min(open, close) - low)}
        stroke={color}
        strokeWidth={1}
      />
      {/* Body */}
      <rect
        x={x}
        y={bodyY}
        width={width}
        height={bodyHeight}
        fill={color}
        stroke={color}
      />
    </g>
  );
};

const CoinGraphic = () => {
  // Initialize with dummy data
  const [data, setData] = useState(() => {
    return Array.from({ length: 20 }, (_, index) => {
      const basePrice = 50000 + Math.random() * 1000;
      return {
        time: new Date(Date.now() - (20 - index) * 60000).toISOString(),
        open: basePrice,
        high: basePrice + Math.random() * 500,
        low: basePrice - Math.random() * 500,
        close: basePrice + (Math.random() - 0.5) * 800,
        volume: Math.floor(Math.random() * 1000)
      };
    });
  });

  // Update data every 5 seconds
  useEffect(() => {
    const interval = setInterval(() => {
      setData(prevData => {
        const lastCandle = prevData[prevData.length - 1];
        const newPrice = lastCandle.close + (Math.random() - 0.5) * 1000;
        
        const newCandle = {
          time: new Date().toISOString(),
          open: lastCandle.close,
          high: newPrice + Math.random() * 500,
          low: newPrice - Math.random() * 500,
          close: newPrice,
          volume: Math.floor(Math.random() * 1000)
        };

        return [...prevData.slice(1), newCandle];
      });
    }, 5000);

    return () => clearInterval(interval);
  }, []);

  // Format time for X-axis
  const formatTime = (time) => {
    const date = new Date(time);
    const day = date.getDate();
    const month = months[String(date.getMonth() + 1).padStart(2, '0')];
    return `${day} ${month}`;
  };

  return (
    <div style={{ height: "100%", minWidth: "calc(100vh - 55px)", overflowY: "auto", overflowX: "auto" }}>
      <div>
        <h1>BTC/USD Candlestick Chart</h1>
      </div>
      <ComposedChart
        width={800}
        height={400}
        data={data}
        margin={{ top: 20, right: 30, left: 20, bottom: 10 }}
      >
        <XAxis 
          dataKey="time" 
          tickFormatter={formatTime}
          angle={-45}
          textAnchor="end"
          height={60}
        />
        <YAxis 
          domain={['auto', 'auto']}
          tickFormatter={(value) => `$${value.toFixed(2)}`}
        />
        <Tooltip 
          formatter={(value, name) => [`$${value.toFixed(2)}`, name]}
          labelFormatter={formatTime}
        />
        <Legend />
        <Bar
          dataKey="close"
          shape={<CustomCandlestick />}
          maxBarSize={10}
        />
      </ComposedChart>
    </div>
  );
};

export default CoinGraphic;